<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use App\Models\IndikatorRuangan;
use App\Models\IndikatorMutu;
use App\Models\MutuRuangan;
use Illuminate\Support\Facades\DB; 
use Carbon\Carbon;

class RekapPerIndikatorExport implements FromView, WithTitle, WithEvents
{
    protected $kategori;
    protected $tahun;

    public function __construct($kategori, $tahun)
    {
        $this->kategori = $kategori;
        $this->tahun = $tahun;
    }

    public function title(): string
    {
        return 'Rekap Tahunan ' . $this->tahun;
    }

    public function view(): View
    {
        $data = collect();

        if ($this->kategori === 'Indikator Mutu Prioritas Unit') {

            $rawIndicators = IndikatorRuangan::query()
                ->whereHas('indikatorMutu.kategori', function ($query) {
                    $query->where('kategori', $this->kategori);
                })
                ->where('active', true)
                ->with([
                    'ruangan',
                    'indikatorMutu',
                    'mutuRuangan' => function ($query) {
                        $query->whereYear('tanggal', $this->tahun);
                    }
                ])
                ->orderBy('id_ruangan', 'asc')
                ->get();

            $data = $rawIndicators->groupBy(function ($item) {
                return $item->ruangan->nama_ruangan;
            })->map(function ($items) {
                return $items->map(function ($indicator) {
                    $monthlyDataGrouped = $indicator->mutuRuangan->groupBy(function ($mutu) {
                        return (int) date('n', strtotime($mutu->tanggal));
                    });

                    return (object) [
                        'judul' => $indicator->indikatorMutu->variabel,
                        'standar' => $indicator->indikatorMutu->standar,
                        'data_bulan' => $this->calculateMonthlyStats($monthlyDataGrouped),
                        'data_tw' => $this->calculateTriwulanStats($monthlyDataGrouped),
                    ];
                });
            });

        }
        else {
            $masterIndicators = IndikatorMutu::query()
                ->whereHas('kategori', function ($q) {
                    $q->where('kategori', $this->kategori);
                })
                ->where('variabel', 'NOT LIKE', '%Kepuasan Masyarakat%')
                ->get();

            $data = $masterIndicators->map(function ($indMaster) {
                $relatedIndikatorRuanganIds = IndikatorRuangan::where('id_indikator', $indMaster->id_indikator)
                    ->pluck('id_indikator_ruangan');

                $allMutuData = MutuRuangan::whereIn('id_indikator_ruangan', $relatedIndikatorRuanganIds)
                    ->whereYear('tanggal', $this->tahun)
                    ->get();

                $monthlyDataGrouped = $allMutuData->groupBy(function ($item) {
                    return (int) date('n', strtotime($item->tanggal));
                });

                return (object) [
                    'judul' => $indMaster->variabel,
                    'standar' => $indMaster->standar,
                    'data_bulan' => $this->calculateMonthlyStats($monthlyDataGrouped),
                    'data_tw' => $this->calculateTriwulanStats($monthlyDataGrouped),
                ];
            });

            if ($this->kategori === 'Indikator Nasional Mutu') {
                $skmObject = $this->calculateGlobalSkmYearly($this->tahun);
                if ($skmObject) {
                    $data->push($skmObject);
                }
            }
        }

        return view('exports.rekap_per_indikator', [
            'data' => $data,
            'kategori' => $this->kategori,
            'tahun' => $this->tahun
        ]);
    }

    private function calculateGlobalSkmYearly($year)
    {
        $skmIndicatorDB = DB::table('indikator_mutu')
            ->where('variabel', 'LIKE', '%Kepuasan Masyarakat%')
            ->first();

        $judulSKM = $skmIndicatorDB ? $skmIndicatorDB->variabel : 'Kepuasan Masyarakat';
        $standarSKM = $skmIndicatorDB ? $skmIndicatorDB->standar : '> 76.61';

        $maxScores = DB::table('pilihan_jawaban')
            ->select('id_pertanyaan', DB::raw('MAX(nilai) as max_nilai'))
            ->groupBy('id_pertanyaan')
            ->pluck('max_nilai', 'id_pertanyaan');

        $skmAnswers = DB::table('jawaban')
            ->join('pilihan_jawaban', 'jawaban.id_pilihan', '=', 'pilihan_jawaban.id_pilihan')
            ->select('jawaban.tanggal', 'jawaban.id_pertanyaan', 'pilihan_jawaban.nilai')
            ->whereYear('jawaban.tanggal', $year)
            ->get();

        if ($skmAnswers->isEmpty()) {
            return (object) [
                'judul' => $judulSKM,
                'standar' => $standarSKM,
                'data_bulan' => array_fill(1, 12, null),
                'data_tw' => array_fill(1, 4, null),
            ];
        }

        $answersByMonth = $skmAnswers->groupBy(function ($item) {
            return (int) Carbon::parse($item->tanggal)->format('n');
        });

        $monthlyStats = [];
        $monthlyRawData = [];

        for ($m = 1; $m <= 12; $m++) {
            if (isset($answersByMonth[$m])) {
                $totalActual = 0;
                $totalMax = 0;

                foreach ($answersByMonth[$m] as $ans) {
                    $totalActual += $ans->nilai;
                    $totalMax += $maxScores[$ans->id_pertanyaan] ?? 0;
                }

                $monthlyStats[$m] = $totalMax > 0 ? round(($totalActual / $totalMax) * 100, 2) : 0;
                $monthlyRawData[$m] = ['num' => $totalActual, 'denum' => $totalMax];
            } else {
                $monthlyStats[$m] = null;
                $monthlyRawData[$m] = ['num' => 0, 'denum' => 0];
            }
        }

        $twStats = [];
        $quarters = [
            1 => [1, 2, 3],
            2 => [4, 5, 6],
            3 => [7, 8, 9],
            4 => [10, 11, 12],
        ];

        foreach ($quarters as $tw => $months) {
            $twNum = 0;
            $twDenum = 0;
            $hasData = false;

            foreach ($months as $m) {
                if (isset($monthlyRawData[$m]) && $monthlyRawData[$m]['denum'] > 0) {
                    $twNum += $monthlyRawData[$m]['num'];
                    $twDenum += $monthlyRawData[$m]['denum'];
                    $hasData = true;
                }
            }

            $twStats[$tw] = ($hasData && $twDenum > 0) ? round(($twNum / $twDenum) * 100, 2) : null;
        }

        return (object) [
            'judul' => $judulSKM, 
            'standar' => $standarSKM,
            'data_bulan' => $monthlyStats,
            'data_tw' => $twStats,
        ];
    }

    private function calculateMonthlyStats($groupedData)
    {
        $monthlyAverages = [];
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            if (isset($groupedData[$bulan])) {
                $data = $groupedData[$bulan];
                $totalSesuai = $data->sum('pasien_sesuai');
                $totalPasien = $data->sum('total_pasien');

                $monthlyAverages[$bulan] = ($totalPasien > 0)
                    ? round(($totalSesuai / $totalPasien) * 100, 2)
                    : null;
            } else {
                $monthlyAverages[$bulan] = null;
            }
        }
        return $monthlyAverages;
    }

    private function calculateTriwulanStats($groupedData)
    {
        $twStats = [];
        $quarters = [
            1 => [1, 2, 3],
            2 => [4, 5, 6],
            3 => [7, 8, 9],
            4 => [10, 11, 12],
        ];

        foreach ($quarters as $tw => $months) {
            $totalSesuaiTW = 0;
            $totalPasienTW = 0;
            $hasData = false;

            foreach ($months as $m) {
                if (isset($groupedData[$m])) {
                    $totalSesuaiTW += $groupedData[$m]->sum('pasien_sesuai');
                    $totalPasienTW += $groupedData[$m]->sum('total_pasien');
                    $hasData = true;
                }
            }

            if ($hasData && $totalPasienTW > 0) {
                $twStats[$tw] = round(($totalSesuaiTW / $totalPasienTW) * 100, 2);
            } else {
                $twStats[$tw] = null;
            }
        }
        return $twStats;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $isImpu = ($this->kategori === 'Indikator Mutu Prioritas Unit');
                $highestRow = $sheet->getHighestRow();

                $sheet->getColumnDimension('A')->setWidth(5); 
    
                if ($isImpu) {
                    $sheet->getColumnDimension('B')->setWidth(20); 
                    $sheet->getColumnDimension('C')->setWidth(50); 
                    $sheet->getColumnDimension('D')->setWidth(15); 
    
                    $startBulan = 'E';
                    $endBulan = 'P';
                    $startTW = 'Q';
                    $endTW = 'T';
                    $lastCol = 'T';

                    $sheet->getStyle('B5:B' . $highestRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle('B5:B' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                } else {
                    $sheet->getColumnDimension('B')->setWidth(50);
                    $sheet->getColumnDimension('C')->setWidth(15); 
    
                    $startBulan = 'D';
                    $endBulan = 'O';
                    $startTW = 'P';
                    $endTW = 'S';
                    $lastCol = 'S';
                }

                $colJudul = $isImpu ? 'C' : 'B';
                $sheet->getStyle($colJudul)->getAlignment()->setWrapText(true);

                foreach (range($startBulan, $endBulan) as $col) {
                    $sheet->getColumnDimension($col)->setWidth(10);
                }
                foreach (range($startTW, $endTW) as $col) {
                    $sheet->getColumnDimension($col)->setWidth(13);
                }

                $sheet->getStyle("A4:{$lastCol}4")->getFont()->setBold(true);
                $sheet->getStyle("A4:{$lastCol}1000")->getAlignment()->setVertical('center');

                $colTw1 = $startTW;
                $colTw2 = ++$startTW;
                $colTw3 = ++$startTW;
                $colTw4 = ++$startTW;

                $sheet->getStyle("{$colTw1}4:{$colTw1}{$highestRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2CC');
                $sheet->getStyle("{$colTw2}4:{$colTw2}{$highestRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FCE4D6');
                $sheet->getStyle("{$colTw3}4:{$colTw3}{$highestRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('EAD1DC');
                $sheet->getStyle("{$colTw4}4:{$colTw4}{$highestRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('F4CCCC');
            },
        ];
    }
}