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

    public function __construct($bulan, $tahun)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
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

                // 1. List ID Pertanyaan untuk menghitung jumlah kolom
                $listPertanyaan = DB::table('pilihan_jawaban')
                    ->distinct()
                    ->orderBy('id_pertanyaan')
                    ->pluck('id_pertanyaan');

                $totalKolom = count($listPertanyaan);

                // Huruf kolom terakhir (Awal kolom C + jumlah pertanyaan + 1 untuk Total)
                // Kolom A=No, Kolom B=No akan dilewati (karena data mulai B? Tidak, data kita mulai A)
                // Struktur: A(No), B(Q1), C(Q2)... Z(Qn), AA(Total)
    
                // Hitung huruf kolom terakhir
                $lastColumnIndex = 1 + $totalKolom + 1; // 1(No) + Qs + 1(Total)
                $lastColumn = Coordinate::stringFromColumnIndex($lastColumnIndex);

                // 2. Styling Header (Baris 4 dan 5)
                $sheet->getStyle('A4:' . $lastColumn . '5')->getFont()->setBold(true);
                $sheet->getStyle('A4:' . $lastColumn . '5')->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A4:' . $lastColumn . '5')->getAlignment()->setVertical('center');

                // 3. Border untuk seluruh tabel
                // Kita cari baris terakhir data
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle('A4:' . $lastColumn . $highestRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                // 4. Lebar Kolom
                $sheet->getColumnDimension('A')->setWidth(5); // No
    
                // Loop kolom pertanyaan (Mulai index 2 / Kolom B)
                for ($i = 2; $i <= $lastColumnIndex; $i++) {
                    $colLetter = Coordinate::stringFromColumnIndex($i);
                    $sheet->getColumnDimension($colLetter)->setWidth(8);
                }

                // Kolom Terakhir (Rata-rata IKM) agak lebar
                $sheet->getColumnDimension($lastColumn)->setWidth(15);
            },
        ];
    }

    public function view(): View
    {
        // 1. Ambil List Pertanyaan (Dinamis)
        $listPertanyaan = DB::table('pilihan_jawaban')
            ->distinct()
            ->orderBy('id_pertanyaan')
            ->pluck('id_pertanyaan');

        // 2. Ambil Data Jawaban
        $jawabanPasien = DB::table('jawaban')
            ->join('bio_pasien', 'jawaban.id_pasien', '=', 'bio_pasien.id_pasien')
            ->leftJoin('pilihan_jawaban', 'jawaban.id_pilihan', '=', 'pilihan_jawaban.id_pilihan')
            ->select(
                'bio_pasien.id_pasien',
                'jawaban.id_pertanyaan',
                'pilihan_jawaban.nilai'
            )
            ->whereYear('jawaban.tanggal', $this->tahun)
            ->whereMonth('jawaban.tanggal', $this->bulan)
            ->whereIn('jawaban.id_pertanyaan', $listPertanyaan)
            ->get();

        // 3. Olah Data
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

        // Hitung Total per Pasien
        foreach ($dataRekap as $id_pasien => $data) {
            $dataRekap[$id_pasien]['total_nilai_ikm'] = array_sum($data['jawaban']);
        }

        return view('exports.rekap_skm_excel', [
            'dataRekap' => $dataRekap,
            'listPertanyaan' => $listPertanyaan,
            'bulan' => $this->bulan,
            'tahun' => $this->tahun
        ]);
    }
}