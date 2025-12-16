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

class RekapSkmExport implements FromView, WithTitle, WithEvents
{
    protected $bulan;
    protected $tahun;
    protected $ruanganId;

    public function __construct($bulan, $tahun, $ruanganId = null)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
        $this->ruanganId = $ruanganId;
    }

    public function title(): string
    {
        return 'Rekap SKM ' . $this->bulan . '-' . $this->tahun;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                $listPertanyaan = DB::table('pilihan_jawaban')
                    ->join('pertanyaan', 'pilihan_jawaban.id_pertanyaan', '=', 'pertanyaan.id_pertanyaan')
                    ->select('pilihan_jawaban.id_pertanyaan', 'pertanyaan.urutan')
                    ->distinct()
                    ->orderBy('pertanyaan.urutan', 'asc')
                    ->pluck('pilihan_jawaban.id_pertanyaan');

                $totalKolom = count($listPertanyaan);

                $lastColumnIndex = 1 + $totalKolom + 1;
                $lastColumn = Coordinate::stringFromColumnIndex($lastColumnIndex);

                $sheet->getStyle('A4:' . $lastColumn . '6')->getFont()->setBold(true);
                $sheet->getStyle('A4:' . $lastColumn . '6')->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A4:' . $lastColumn . '6')->getAlignment()->setVertical('center');

                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle('A4:' . $lastColumn . $highestRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                $sheet->getColumnDimension('A')->setWidth(5); 
                for ($i = 2; $i <= $lastColumnIndex; $i++) {
                    $colLetter = Coordinate::stringFromColumnIndex($i);
                    $sheet->getColumnDimension($colLetter)->setWidth(8);
                }
                $sheet->getColumnDimension($lastColumn)->setWidth(15);
            },
        ];
    }

    public function view(): View
    {
        $listPertanyaan = DB::table('pilihan_jawaban')
            ->join('pertanyaan', 'pilihan_jawaban.id_pertanyaan', '=', 'pertanyaan.id_pertanyaan')
            ->select('pilihan_jawaban.id_pertanyaan', 'pertanyaan.urutan')
            ->distinct()
            ->orderBy('pertanyaan.urutan', 'asc')
            ->pluck('pilihan_jawaban.id_pertanyaan');

        $queryJawaban = DB::table('jawaban')
            ->join('bio_pasien', 'jawaban.id_pasien', '=', 'bio_pasien.id_pasien')
            ->leftJoin('pilihan_jawaban', 'jawaban.id_pilihan', '=', 'pilihan_jawaban.id_pilihan')
            ->select(
                'bio_pasien.id_pasien',
                'jawaban.id_pertanyaan',
                'pilihan_jawaban.nilai'
            )
            ->whereYear('jawaban.tanggal', $this->tahun)
            ->whereMonth('jawaban.tanggal', $this->bulan)
            ->whereIn('jawaban.id_pertanyaan', $listPertanyaan);

        if ($this->ruanganId) {
            $queryJawaban->where('bio_pasien.id_ruangan', $this->ruanganId);
        }

        $jawabanPasien = $queryJawaban->get();

        $dataRekap = [];
        foreach ($jawabanPasien as $jawaban) {
            if (!isset($dataRekap[$jawaban->id_pasien])) {
                $dataRekap[$jawaban->id_pasien] = [
                    'jawaban' => [],
                    'total_nilai_ikm' => 0
                ];
            }
            $dataRekap[$jawaban->id_pasien]['jawaban'][$jawaban->id_pertanyaan] = $jawaban->nilai;
        }

        foreach ($dataRekap as $id_pasien => $data) {
            $dataRekap[$id_pasien]['total_nilai_ikm'] = array_sum($data['jawaban']);
        }

        $namaRuangan = 'Semua Ruangan';
        if ($this->ruanganId) {
            $namaRuangan = DB::table('ruangan')->where('id_ruangan', $this->ruanganId)->value('nama_ruangan');
        }

        return view('exports.rekap_skm_excel', [
            'dataRekap' => $dataRekap,
            'listPertanyaan' => $listPertanyaan,
            'bulan' => $this->bulan,
            'tahun' => $this->tahun,
            'namaRuangan' => $namaRuangan // Kirim variable baru
        ]);
    }
}