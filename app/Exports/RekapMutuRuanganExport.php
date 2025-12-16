<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RekapMutuRuanganExport implements FromView, WithTitle, WithEvents
{
    protected $ruanganId;
    protected $bulan;
    protected $tahun;

    public function __construct($ruanganId, $bulan, $tahun)
    {
        $this->ruanganId = $ruanganId;
        $this->bulan = (int) $bulan;
        $this->tahun = (int) $tahun;
    }

    public function title(): string
    {
        return 'Rekap ' . Carbon::create()->month($this->bulan)->translatedFormat('M') . ' ' . $this->tahun;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $jumlahHari = Carbon::createFromDate($this->tahun, $this->bulan)->daysInMonth;

                $sheet->getColumnDimension('A')->setWidth(5);

                $sheet->getColumnDimension('B')->setWidth(50);
                $sheet->getStyle('B')->getAlignment()->setWrapText(true);

                $startColumnIndex = 3;
                for ($i = 0; $i < $jumlahHari; $i++) {
                    $columnIndex = $startColumnIndex + $i;
                    $columnLetter = Coordinate::stringFromColumnIndex($columnIndex);
                    $sheet->getColumnDimension($columnLetter)->setWidth(6);
                }

                $lastDateIndex = $startColumnIndex + $jumlahHari;

                $colJumlah = Coordinate::stringFromColumnIndex($lastDateIndex);
                $sheet->getColumnDimension($colJumlah)->setWidth(10);

                $colPersen = Coordinate::stringFromColumnIndex($lastDateIndex + 1);
                $sheet->getColumnDimension($colPersen)->setWidth(10);
            },
        ];
    }

    public function view(): View
    {
        $ruangan = DB::table('ruangan')->where('id_ruangan', $this->ruanganId)->first();
        $jumlahHari = Carbon::createFromDate($this->tahun, $this->bulan)->daysInMonth;

        $indikatorList = DB::table('indikator_ruangan')
            ->join('indikator_mutu', 'indikator_ruangan.id_indikator', '=', 'indikator_mutu.id_indikator')
            ->where('indikator_ruangan.id_ruangan', $this->ruanganId)
            ->where('indikator_ruangan.active', true)
            ->select('indikator_ruangan.id_indikator_ruangan', 'indikator_mutu.variabel')
            ->orderBy('indikator_mutu.id_indikator', 'asc')
            ->get();

        $dataRekap = [];

        foreach ($indikatorList as $index => $indikator) {
            $row = [
                'no' => $index + 1,
                'variabel' => $indikator->variabel,
                'harian' => [],
                'total_num' => 0,
                'total_denum' => 0,
                'persentase' => 0
            ];

            $transaksi = DB::table('mutu_ruangan')
                ->where('id_indikator_ruangan', $indikator->id_indikator_ruangan)
                ->whereMonth('tanggal', $this->bulan)
                ->whereYear('tanggal', $this->tahun)
                ->get()
                ->keyBy(function ($item) {
                    return (int) Carbon::parse($item->tanggal)->format('d');
                });

            for ($tgl = 1; $tgl <= $jumlahHari; $tgl++) {
                if (isset($transaksi[$tgl])) {
                    $num = $transaksi[$tgl]->pasien_sesuai;
                    $denum = $transaksi[$tgl]->total_pasien;

                    $row['harian'][$tgl] = ['num' => $num, 'denum' => $denum];
                    $row['total_num'] += $num;
                    $row['total_denum'] += $denum;
                } else {
                    $row['harian'][$tgl] = null;
                }
            }

            if ($row['total_denum'] > 0) {
                $row['persentase'] = round(($row['total_num'] / $row['total_denum']) * 100, 2);
            }

            $dataRekap[] = $row;
        }

        $maxScores = DB::table('pilihan_jawaban')
            ->select('id_pertanyaan', DB::raw('MAX(nilai) as max_nilai'))
            ->groupBy('id_pertanyaan')
            ->pluck('max_nilai', 'id_pertanyaan');

        $skmAnswers = DB::table('jawaban')
            ->join('pilihan_jawaban', 'jawaban.id_pilihan', '=', 'pilihan_jawaban.id_pilihan')
            ->select('jawaban.tanggal', 'jawaban.id_pertanyaan', 'pilihan_jawaban.nilai')
            ->whereMonth('jawaban.tanggal', $this->bulan)
            ->whereYear('jawaban.tanggal', $this->tahun)
            ->get();

        $rowSKM = [
            'no' => count($dataRekap) + 1,
            'variabel' => 'Kepuasan Masyarakat',
            'harian' => [],
            'total_num' => 0, 
            'total_denum' => 0, 
            'persentase' => 0
        ];

        $groupedSkm = $skmAnswers->groupBy(function ($item) {
            return (int) Carbon::parse($item->tanggal)->format('d');
        });

        // Loop Tanggal
        for ($tgl = 1; $tgl <= $jumlahHari; $tgl++) {
            if (isset($groupedSkm[$tgl])) {
                $dailyActual = 0;
                $dailyMax = 0;

                foreach ($groupedSkm[$tgl] as $ans) {
                    $dailyActual += $ans->nilai;
                    $dailyMax += $maxScores[$ans->id_pertanyaan] ?? 0;
                }

                $rowSKM['harian'][$tgl] = ['num' => $dailyActual, 'denum' => $dailyMax];
                $rowSKM['total_num'] += $dailyActual;
                $rowSKM['total_denum'] += $dailyMax;
            } else {
                $rowSKM['harian'][$tgl] = null;
            }   
        }

        if ($rowSKM['total_denum'] > 0) {
            $rowSKM['persentase'] = round(($rowSKM['total_num'] / $rowSKM['total_denum']) * 100, 2);
        }

        $dataRekap[] = $rowSKM;

        return view('exports.rekap_mutu_per_ruangan', [
            'ruangan' => $ruangan,
            'bulan' => $this->bulan,
            'tahun' => $this->tahun,
            'jumlahHari' => $jumlahHari,
            'data' => $dataRekap,
            'namaBulan' => Carbon::create()->month($this->bulan)->locale('id')->translatedFormat('F')
        ]);
    }
}