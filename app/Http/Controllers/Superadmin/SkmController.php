<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use Carbon\Carbon; 

class SkmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $selectedYear = $request->input('year', Carbon::now()->year);
        $selectedMonth = $request->input('month', Carbon::now()->month);

        $jawabanPasien = DB::table('jawaban')
            ->join('bio_pasien', 'jawaban.id_pasien', '=', 'bio_pasien.id_pasien')
            ->leftJoin('pilihan_jawaban', 'jawaban.id_pilihan', '=', 'pilihan_jawaban.id_pilihan')
            ->select(
                'bio_pasien.id_pasien',
                'bio_pasien.no_rm',
                'jawaban.id_pertanyaan',
                'pilihan_jawaban.nilai'
            )
            ->whereYear('jawaban.tanggal', $selectedYear)
            ->whereMonth('jawaban.tanggal', $selectedMonth)
            ->where('jawaban.id_pertanyaan', '<=', 15)
            ->get();

        // Array untuk menampung data pasien dan jawabannya
        $dataRekap = [];
        // Array untuk menghitung total dan jumlah jawaban per pertanyaan (untuk rata-rata kolom)
        $rataRataKolom = array_fill(1, 15, ['total' => 0, 'count' => 0]);

        foreach ($jawabanPasien as $jawaban) {
            // Inisialisasi data pasien jika belum ada
            if (!isset($dataRekap[$jawaban->id_pasien])) {
                $dataRekap[$jawaban->id_pasien] = [
                    'no_rm' => $jawaban->no_rm,
                    'jawaban' => [],
                    'total_nilai_ikm' => 0 // Tambahkan key untuk total nilai IKM per pasien
                ];
            }
            // Masukkan nilai jawaban
            $dataRekap[$jawaban->id_pasien]['jawaban'][$jawaban->id_pertanyaan] = $jawaban->nilai;

            // Akumulasi data untuk rata-rata kolom
            if (isset($jawaban->nilai)) {
                $rataRataKolom[$jawaban->id_pertanyaan]['total'] += $jawaban->nilai;
                $rataRataKolom[$jawaban->id_pertanyaan]['count']++;
            }
        }

        // Hitung total nilai IKM untuk setiap pasien dan finalisasi rata-rata per kolom
        foreach ($dataRekap as $id_pasien => $data) {
            $dataRekap[$id_pasien]['total_nilai_ikm'] = array_sum($data['jawaban']);
        }

        $finalRataRataKolom = [];
        for ($i = 1; $i <= 15; $i++) {
            if ($rataRataKolom[$i]['count'] > 0) {
                $finalRataRataKolom[$i] = $rataRataKolom[$i]['total'] / $rataRataKolom[$i]['count'];
            } else {
                $finalRataRataKolom[$i] = 0;
            }
        }

        return view('superadmin.skm_rekap', [
            'dataRekap' => $dataRekap,
            'rataRataKolom' => $finalRataRataKolom, // Kirim data rata-rata kolom ke view
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Show the form for editing the survey questions.
     */
    public function editPertanyaan()
    {
        // 1. Ambil semua pertanyaan survei (pertanyaan 1-15 + 16 untuk kritik/saran)
        // Saya asumsikan tabelnya bernama 'pertanyaan'
        $dataPertanyaan = DB::table('pertanyaan')
            ->orderBy('id_pertanyaan') // Urutkan berdasarkan ID
            ->get();

        // 2. Ambil semua pilihan jawaban dan kelompokkan berdasarkan id_pertanyaan
        // Saya asumsikan tabelnya bernama 'pilihan_jawaban'
        $dataPilihan = DB::table('pilihan_jawaban')
            ->orderBy('id_pertanyaan')
            ->orderBy('nilai') // Urutkan pilihan berdasarkan nilai (A, B, C, D)
            ->get()
            ->groupBy('id_pertanyaan');

        // 3. Gabungkan pertanyaan dengan pilihan jawabannya
        $surveyData = $dataPertanyaan->map(function ($pertanyaan) use ($dataPilihan) {
            // Lampirkan pilihan jawaban ke pertanyaan ini
            // Jika tidak ada pilihan (misal: isian teks), akan jadi collection kosong
            $pertanyaan->pilihan = $dataPilihan->get($pertanyaan->id_pertanyaan, collect());

            // Tambahkan asumsi tipe pertanyaan berdasarkan ID (bisa disesuaikan)
            if ($pertanyaan->id_pertanyaan == 16) { // ID 16 biasanya untuk Kritik/Saran
                $pertanyaan->tipe_pertanyaan = 'Isian Teks';
            } else {
                $pertanyaan->tipe_pertanyaan = 'Pilihan Ganda'; // Asumsi default
            }

            return $pertanyaan;
        });

        // 4. Kirim data yang sudah tersusun ke view
        return view('superadmin.skm_edit2', compact('surveyData'));
    }

    /**
     * Display the survey results with charts and lists.
     */
    public function hasil()
    {
        // === 1. DATA DASAR ===
        // Ambil semua id_pasien unik yang pernah mengisi survei
        $respondenIds = DB::table('jawaban')->distinct()->pluck('id_pasien');
        $totalResponden = $respondenIds->count();

        // Ambil data bio dari semua responden
        $bioResponden = DB::table('bio_pasien')->whereIn('id_pasien', $respondenIds)->get();

        // === 2. DATA UNTUK LIST ===
        $listNoRm = $bioResponden->pluck('no_rm');
        $listUmur = $bioResponden->pluck('umur');
        $listKritikSaran = DB::table('jawaban')
            ->where('id_pertanyaan', 16) // Asumsi ID 16 adalah untuk kritik & saran
            ->whereNotNull('hasil_nilai')
            ->pluck('hasil_nilai');

        // === 3. DATA UNTUK CHARTS ===

        // Chart Nama Ruangan
        $ruanganData = DB::table('bio_pasien as bp')
            ->join('ruangan as r', 'bp.id_ruangan', '=', 'r.id_ruangan')
            ->whereIn('bp.id_pasien', $respondenIds)
            ->select('r.nama_ruangan', DB::raw('count(*) as total'))
            ->groupBy('r.nama_ruangan')
            ->pluck('total', 'nama_ruangan');

        $ruanganChart = [
            'labels' => $ruanganData->keys(),
            'data' => $ruanganData->values()
        ];

        // Chart Jenis Kelamin
        $jenisKelaminData = $bioResponden->countBy('jenis_kelamin');
        $jenisKelaminChart = [
            'labels' => $jenisKelaminData->keys(),
            'data' => $jenisKelaminData->values()
        ];

        // Chart Pendidikan
        $pendidikanData = $bioResponden->countBy('pendidikan');
        $pendidikanChart = [
            'labels' => $pendidikanData->keys(),
            'data' => $pendidikanData->values()
        ];

        // Chart Pekerjaan
        $pekerjaanData = $bioResponden->countBy('pekerjaan');
        $pekerjaanChart = [
            'labels' => $pekerjaanData->keys(),
            'data' => $pekerjaanData->values()
        ];

        // Chart Pertanyaan Pelayanan Publik (Asumsi id_pertanyaan = 10)
        $pelayananData = DB::table('jawaban as j')
            ->join('pilihan_jawaban as pj', 'j.id_pilihan', '=', 'pj.id_pilihan')
            ->where('j.id_pertanyaan', 10)
            ->select('pj.pilihan', DB::raw('count(*) as total'))
            ->groupBy('pj.pilihan')
            ->pluck('total', 'pj.pilihan');

        $pelayananChart = [
            'labels' => $pelayananData->keys(),
            'data' => $pelayananData->values()
        ];

        // Chart Pertanyaan Keselamatan Pasien (Asumsi id_pertanyaan = 11)
        $keselamatanData = DB::table('jawaban as j')
            ->join('pilihan_jawaban as pj', 'j.id_pilihan', '=', 'pj.id_pilihan')
            ->where('j.id_pertanyaan', 11)
            ->select('pj.pilihan', DB::raw('count(*) as total'))
            ->groupBy('pj.pilihan')
            ->pluck('total', 'pj.pilihan');

        $keselamatanChart = [
            'labels' => $keselamatanData->keys(),
            'data' => $keselamatanData->values()
        ];

        // === 4. KIRIM SEMUA DATA KE VIEW ===
        return view('superadmin.skm_hasil', compact(
            'totalResponden',
            'listNoRm',
            'listUmur',
            'listKritikSaran',
            'ruanganChart',
            'jenisKelaminChart',
            'pendidikanChart',
            'pekerjaanChart',
            'pelayananChart',
            'keselamatanChart'
        ));
    }
}
