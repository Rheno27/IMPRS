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
     * Menyimpan perubahan pada struktur pertanyaan survei.
     */
    public function updatePertanyaan(Request $request)
    {
        $submittedQuestions = $request->input('questions', []);

        // Array untuk melacak ID yang "aman" (disubmit oleh form)
        $safePertanyaanIds = [];
        $safePilihanIds = [];

        try {
            DB::transaction(function () use ($submittedQuestions, &$safePertanyaanIds, &$safePilihanIds) {

                foreach ($submittedQuestions as $qData) {

                    $pertanyaanId = null;
                    $pertanyaanData = [
                        'pertanyaan' => $qData['pertanyaan'] ?? 'Pertanyaan Kosong',
                        // 'tipe_pertanyaan' => $qData['tipe'] // HANYA JIKA ADA KOLOMNYA DI DB
                    ];

                    // Cek apakah ini UPDATE (ada ID) atau INSERT (ID kosong)
                    if (!empty($qData['id_pertanyaan'])) {
                        // --- Ini adalah UPDATE Pertanyaan ---
                        $pertanyaanId = $qData['id_pertanyaan'];
                        DB::table('pertanyaan')
                            ->where('id_pertanyaan', $pertanyaanId)
                            ->update($pertanyaanData);
                    } else {
                        // --- Ini adalah INSERT Pertanyaan Baru ---
                        $pertanyaanId = DB::table('pertanyaan')->insertGetId($pertanyaanData);
                    }

                    // Catat ID pertanyaan ini sebagai "aman"
                    $safePertanyaanIds[] = $pertanyaanId;

                    // --- Proses Pilihan Jawabannya ---
                    if (($qData['tipe'] ?? 'Pilihan Ganda') != 'Isian Teks' && isset($qData['pilihan']) && is_array($qData['pilihan'])) {

                        foreach ($qData['pilihan'] as $pData) {

                            $pilihanId = null;
                            $pilihanData = [
                                'id_pertanyaan' => $pertanyaanId,
                                'pilihan' => $pData['pilihan'] ?? 'Pilihan Kosong',
                                'nilai' => $pData['nilai'] ?? 0
                            ];

                            // Cek apakah ini UPDATE Pilihan atau INSERT Pilihan Baru
                            if (!empty($pData['id_pilihan'])) {
                                // --- UPDATE Pilihan ---
                                $pilihanId = $pData['id_pilihan'];
                                DB::table('pilihan_jawaban')
                                    ->where('id_pilihan', $pData['id_pilihan'])
                                    ->update($pilihanData);
                            } else {
                                // --- INSERT Pilihan Baru ---
                                $pilihanId = DB::table('pilihan_jawaban')->insertGetId($pilihanData);
                            }
                            // Catat ID pilihan ini sebagai "aman"
                            $safePilihanIds[] = $pilihanId;
                        }
                    }
                } // --- Akhir loop pertanyaan ---


                // --- LOGIKA HAPUS PILIHAN JAWABAN (YANG AMAN) ---
                // UI Anda tidak punya tombol hapus pertanyaan, jadi kita tidak hapus pertanyaan.
                // Tapi UI Anda punya tombol hapus pilihan.

                // 1. Ambil semua ID pilihan yang terkait dengan pertanyaan yang baru saja kita proses
                $existingPilihanIds = DB::table('pilihan_jawaban')
                    ->whereIn('id_pertanyaan', $safePertanyaanIds)
                    ->pluck('id_pilihan');

                // 2. Cari ID mana yang ada di DB tapi TIDAK disubmit (berarti dihapus di UI)
                $pilihanIdsToDelete = $existingPilihanIds->diff($safePilihanIds);

                if ($pilihanIdsToDelete->isNotEmpty()) {
                    // 3. CEK KE TABEL JAWABAN. Ini adalah bagian PENTING.
                    $checkJawaban = DB::table('jawaban')
                        ->whereIn('id_pilihan', $pilihanIdsToDelete)
                        ->count();

                    if ($checkJawaban > 0) {
                        // JIKA SUDAH ADA YANG JAWAB, GAGALKAN SEMUA PROSES
                        throw new \Exception(
                            "GAGAL: Anda mencoba menghapus pilihan jawaban yang sudah pernah dipilih oleh responden. " .
                            "Data responden tidak akan dihapus. Perubahan tidak disimpan."
                        );
                    }

                    // 4. Aman untuk dihapus (karena belum ada yg jawab)
                    DB::table('pilihan_jawaban')->whereIn('id_pilihan', $pilihanIdsToDelete)->delete();
                }

            }); // --- Akhir Transaction ---

        } catch (\Exception $e) {
            // Jika terjadi error (terutama error foreign key), kirim pesan error
            return redirect()->route('superadmin.skm_edit2')
                ->with('error', $e->getMessage());
        }

        // Redirect kembali dengan pesan sukses
        return redirect()->route('superadmin.skm_edit2')
            ->with('success', 'Struktur pertanyaan survei berhasil diperbarui.');
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

        // === 3. DATA UNTUK CHARTS DEMOGRAFI ===

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

        // === 4. DATA BARU UNTUK SEMUA PERTANYAAN SURVEI (1-15) ===

        // Ambil semua pertanyaan (asumsi 1-15 adalah Pilihan Ganda SKM)
        $pertanyaanSurvei = DB::table('pertanyaan')
            ->where('id_pertanyaan', '<=', 15)
            ->orderBy('id_pertanyaan')
            ->get();

        $allSurveyCharts = [];

        foreach ($pertanyaanSurvei as $pertanyaan) {
            // Query data jawaban untuk pertanyaan ini
            $data = DB::table('jawaban as j')
                ->join('pilihan_jawaban as pj', 'j.id_pilihan', '=', 'pj.id_pilihan')
                ->where('j.id_pertanyaan', $pertanyaan->id_pertanyaan)
                ->whereIn('j.id_pasien', $respondenIds) // Jika $respondenIds kosong, ini akan jadi '0 = 1'

                // --- PERBAIKAN DI SINI ---
                // Kita harus SELECT dan GROUP BY kedua kolom (pilihan dan nilai)
                ->select('pj.pilihan', 'pj.nilai', DB::raw('count(*) as total'))
                ->groupBy('pj.pilihan', 'pj.nilai')
                // --- AKHIR PERBAIKAN ---

                ->orderBy('pj.nilai') // Sekarang orderBy ini valid
                ->pluck('total', 'pilihan');

            // Simpan data untuk dikirim ke view
            $allSurveyCharts[] = [
                'id_pertanyaan' => $pertanyaan->id_pertanyaan,
                'pertanyaan_text' => $pertanyaan->pertanyaan,
                'chart' => [
                    'labels' => $data->keys(),
                    'data' => $data->values()
                ]
            ];
        }

        // === 5. KIRIM SEMUA DATA KE VIEW ===
        return view('superadmin.skm_hasil', compact(
            'totalResponden',
            'listNoRm',
            'listUmur',
            'listKritikSaran',
            'ruanganChart',
            'jenisKelaminChart',
            'pendidikanChart',
            'pekerjaanChart',
            'allSurveyCharts' // <-- Variabel baru yang berisi semua data chart
        ));
    }
}
