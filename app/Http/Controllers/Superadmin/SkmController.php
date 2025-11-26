<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\RekapSkmExport;
use Maatwebsite\Excel\Facades\Excel;
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

        // === PERBAIKAN DI SINI ===
        // Jangan cari kolom 'tipe_pertanyaan'.
        // Cari ID pertanyaan yang PUNYA OPSI di tabel 'pilihan_jawaban'.
        // Otomatis pertanyaan 'Kritik Saran' (yang tidak punya opsi A/B/C/D) tidak akan terambil.

        $listPertanyaan = DB::table('pilihan_jawaban')
            ->join('pertanyaan', 'pilihan_jawaban.id_pertanyaan', '=', 'pertanyaan.id_pertanyaan')
            ->select('pilihan_jawaban.id_pertanyaan', 'pertanyaan.urutan') // <--- PENTING: Ambil kolom urutan
            ->distinct()
            ->orderBy('pertanyaan.urutan', 'asc')
            ->get() // Eksekusi query dulu (ambil semua data)
            ->pluck('id_pertanyaan');

        // === Query Data Jawaban ===
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
            ->whereIn('jawaban.id_pertanyaan', $listPertanyaan) // Filter ID yang valid
            ->get();

        $dataRekap = [];

        // Siapkan array rata-rata (Default 0)
        $rataRataKolom = [];
        foreach ($listPertanyaan as $id) {
            $rataRataKolom[$id] = ['total' => 0, 'count' => 0];
        }

        foreach ($jawabanPasien as $jawaban) {
            if (!isset($dataRekap[$jawaban->id_pasien])) {
                $dataRekap[$jawaban->id_pasien] = [
                    'no_rm' => $jawaban->no_rm,
                    'jawaban' => [],
                    'total_nilai_ikm' => 0
                ];
            }

            $dataRekap[$jawaban->id_pasien]['jawaban'][$jawaban->id_pertanyaan] = $jawaban->nilai;

            if (isset($jawaban->nilai) && isset($rataRataKolom[$jawaban->id_pertanyaan])) {
                $rataRataKolom[$jawaban->id_pertanyaan]['total'] += $jawaban->nilai;
                $rataRataKolom[$jawaban->id_pertanyaan]['count']++;
            }
        }

        // Hitung total per pasien
        foreach ($dataRekap as $id_pasien => $data) {
            $dataRekap[$id_pasien]['total_nilai_ikm'] = array_sum($data['jawaban']);
        }

        // Hitung rata-rata per kolom (pertanyaan)
        $finalRataRataKolom = [];
        foreach ($listPertanyaan as $id) {
            if ($rataRataKolom[$id]['count'] > 0) {
                $finalRataRataKolom[$id] = $rataRataKolom[$id]['total'] / $rataRataKolom[$id]['count'];
            } else {
                $finalRataRataKolom[$id] = 0;
            }
        }

        return view('superadmin.skm_rekap', [
            'dataRekap' => $dataRekap,
            'rataRataKolom' => $finalRataRataKolom,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'listPertanyaan' => $listPertanyaan // Kirim list ID pertanyaan dinamis
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
    public function destroyPertanyaan($id)
    {
        try {
            // 1. Cek Safety: Apakah pertanyaan ini sudah ada di tabel jawaban?
            $cekResponden = DB::table('jawaban')
                ->where('id_pertanyaan', $id)
                ->exists();

            if ($cekResponden) {
                // GAGAL: Kembalikan JSON error
                return response()->json([
                    'status' => 'error',
                    'message' => 'GAGAL: Pertanyaan tidak bisa dihapus karena sudah memiliki data responden. Data aman.'
                ], 400); // 400 Bad Request
            }

            // 2. Jika Aman, Hapus Pilihan Jawaban (Foreign Key)
            DB::table('pilihan_jawaban')->where('id_pertanyaan', $id)->delete();

            // 3. Hapus Pertanyaan
            DB::table('pertanyaan')->where('id_pertanyaan', $id)->delete();

            // SUKSES: Kembalikan JSON success
            return response()->json([
                'status' => 'success',
                'message' => 'Pertanyaan berhasil dihapus.'
            ]);

        } catch (\Exception $e) {
            // SERVER ERROR
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Show the form for editing the survey questions.
     */
    public function editPertanyaan()
    {
        // UBAH DISINI: Order by 'urutan' dulu, baru 'id_pertanyaan'
        $dataPertanyaan = DB::table('pertanyaan')
            ->orderBy('urutan', 'asc')
            ->orderBy('id_pertanyaan', 'asc')
            ->get();

        $dataPilihan = DB::table('pilihan_jawaban')
            ->orderBy('id_pilihan', 'asc')
            ->get()
            ->groupBy('id_pertanyaan');

        $surveyData = $dataPertanyaan->map(function ($pertanyaan) use ($dataPilihan) {
            $pertanyaan->pilihan = $dataPilihan->get($pertanyaan->id_pertanyaan, collect());

            if ($pertanyaan->id_pertanyaan == 16 || $pertanyaan->pilihan->isEmpty()) {
                $pertanyaan->tipe_pertanyaan = 'Isian Teks';
            } else {
                $pertanyaan->tipe_pertanyaan = 'Pilihan Ganda';
            }
            return $pertanyaan;
        });

        return view('superadmin.skm_edit2', compact('surveyData'));
    }

    /**
     * Menyimpan perubahan pada struktur pertanyaan survei.
     */
    public function updatePertanyaan(Request $request)
    {
        $submittedQuestions = $request->input('questions', []);
        $safePertanyaanIds = [];
        $safePilihanIds = [];

        try {
            DB::transaction(function () use ($submittedQuestions, &$safePertanyaanIds, &$safePilihanIds) {

                // $index adalah urutan visual dari atas ke bawah (0, 1, 2...)
                foreach ($submittedQuestions as $index => $qData) {

                    $pertanyaanId = null;

                    // KITA SIMPAN URUTAN SESUAI POSISI DI LAYAR ($index + 1)
                    $pertanyaanData = [
                        'pertanyaan' => $qData['pertanyaan'] ?? 'Pertanyaan Kosong',
                        'urutan' => $index + 1
                    ];

                    if (!empty($qData['id_pertanyaan'])) {
                        $pertanyaanId = $qData['id_pertanyaan'];
                        DB::table('pertanyaan')->where('id_pertanyaan', $pertanyaanId)->update($pertanyaanData);
                    } else {
                        $pertanyaanId = DB::table('pertanyaan')->insertGetId($pertanyaanData);
                    }

                    $safePertanyaanIds[] = $pertanyaanId;

                    // ... (Bagian Pilihan Jawaban SAMA PERSIS, tidak berubah) ...
                    if (isset($qData['pilihan']) && is_array($qData['pilihan'])) {
                        foreach ($qData['pilihan'] as $pData) {
                            $pilihanId = null;
                            $pilihanData = [
                                'id_pertanyaan' => $pertanyaanId,
                                'pilihan' => $pData['pilihan'] ?? '',
                                'nilai' => isset($pData['nilai']) ? intval($pData['nilai']) : 0
                            ];
                            if (!empty($pData['id_pilihan'])) {
                                $pilihanId = $pData['id_pilihan'];
                                DB::table('pilihan_jawaban')->where('id_pilihan', $pData['id_pilihan'])->update($pilihanData);
                            } else {
                                $pilihanId = DB::table('pilihan_jawaban')->insertGetId($pilihanData);
                            }
                            $safePilihanIds[] = $pilihanId;
                        }
                    }
                }

                // ... (Bagian Hapus Pilihan & Hapus Pertanyaan SAMA PERSIS) ...
                // Hapus Pilihan
                $existingPilihanIds = DB::table('pilihan_jawaban')->whereIn('id_pertanyaan', $safePertanyaanIds)->pluck('id_pilihan');
                $pilihanIdsToDelete = $existingPilihanIds->diff($safePilihanIds);
                if ($pilihanIdsToDelete->isNotEmpty()) {
                    DB::table('pilihan_jawaban')->whereIn('id_pilihan', $pilihanIdsToDelete)->delete();
                }

                // Hapus Pertanyaan
                $questionsToDelete = DB::table('pertanyaan')->whereNotIn('id_pertanyaan', $safePertanyaanIds)->pluck('id_pertanyaan');
                if ($questionsToDelete->isNotEmpty()) {
                    foreach ($questionsToDelete as $delId) {
                        DB::table('jawaban')->where('id_pertanyaan', $delId)->delete();
                        DB::table('pilihan_jawaban')->where('id_pertanyaan', $delId)->delete();
                        DB::table('pertanyaan')->where('id_pertanyaan', $delId)->delete();
                    }
                }
            });
        } catch (\Exception $e) {
            return redirect()->route('superadmin.skm_edit2')->with('error', $e->getMessage());
        }
        return redirect()->route('superadmin.skm_edit2')->with('success', 'Struktur pertanyaan berhasil diperbarui.');
    }

    /**
     * Display the survey results with charts and lists.
     */
    public function hasil()
    {
        // === 1. DATA DASAR ===
        $respondenIds = DB::table('jawaban')->distinct()->pluck('id_pasien');
        $totalResponden = $respondenIds->count();
        $bioResponden = DB::table('bio_pasien')->whereIn('id_pasien', $respondenIds)->get();

        // === 2. PISAHKAN PERTANYAAN (PILIHAN GANDA vs ISIAN TEKS) ===
        // Ambil ID pertanyaan yang punya Pilihan Jawaban (berarti ini untuk Grafik)
        $idsPilihanGanda = DB::table('pilihan_jawaban')
            ->distinct()
            ->pluck('id_pertanyaan');

        // Ambil Kritik Saran (Jawaban dari pertanyaan yang TIDAK punya pilihan ganda)
        $listKritikSaran = DB::table('jawaban')
            ->whereNotIn('id_pertanyaan', $idsPilihanGanda)
            ->whereNotNull('hasil_nilai')
            ->pluck('hasil_nilai');

        // Data List Pendukung
        $listNoRm = $bioResponden->pluck('no_rm');
        $listUmur = $bioResponden->pluck('umur');

        // === 3. DATA DEMOGRAFI (RUANGAN, KELAMIN, PENDIDIKAN, PEKERJAAN) ===

        // Chart Nama Ruangan
        $ruanganData = DB::table('bio_pasien as bp')
            ->join('ruangan as r', 'bp.id_ruangan', '=', 'r.id_ruangan')
            ->whereIn('bp.id_pasien', $respondenIds)
            ->select('r.nama_ruangan', DB::raw('count(*) as total'))
            ->groupBy('r.nama_ruangan')
            ->pluck('total', 'nama_ruangan');
        $ruanganChart = ['labels' => $ruanganData->keys(), 'data' => $ruanganData->values()];

        // Chart Jenis Kelamin
        $jenisKelaminData = $bioResponden->countBy('jenis_kelamin');
        $jenisKelaminChart = ['labels' => $jenisKelaminData->keys(), 'data' => $jenisKelaminData->values()];

        // Chart Pendidikan
        $pendidikanData = $bioResponden->countBy('pendidikan');
        $pendidikanChart = ['labels' => $pendidikanData->keys(), 'data' => $pendidikanData->values()];

        // Chart Pekerjaan
        $pekerjaanData = $bioResponden->countBy('pekerjaan');
        $pekerjaanChart = ['labels' => $pekerjaanData->keys(), 'data' => $pekerjaanData->values()];


        // === 4. DATA HASIL SURVEI (DINAMIS) ===

        // Ambil pertanyaan yang ada di daftar $idsPilihanGanda
        $pertanyaanSurvei = DB::table('pertanyaan')
            ->whereIn('id_pertanyaan', $idsPilihanGanda)
            ->orderBy('urutan', 'asc') // Urutkan berdasarkan posisi yang kamu atur
            ->get();

        $allSurveyCharts = [];

        foreach ($pertanyaanSurvei as $pertanyaan) {
            $data = DB::table('jawaban as j')
                ->join('pilihan_jawaban as pj', 'j.id_pilihan', '=', 'pj.id_pilihan')
                ->where('j.id_pertanyaan', $pertanyaan->id_pertanyaan)
                ->whereIn('j.id_pasien', $respondenIds)
                ->select('pj.pilihan', 'pj.nilai', DB::raw('count(*) as total'))
                ->groupBy('pj.pilihan', 'pj.nilai')
                ->orderBy('pj.nilai')
                ->pluck('total', 'pilihan');

            $allSurveyCharts[] = [
                'id_pertanyaan' => $pertanyaan->id_pertanyaan,
                'pertanyaan_text' => $pertanyaan->pertanyaan,
                'chart' => [
                    'labels' => $data->keys(),
                    'data' => $data->values()
                ]
            ];
        }

        return view('superadmin.skm_hasil', compact(
            'totalResponden',
            'listNoRm',
            'listUmur',
            'listKritikSaran',
            'ruanganChart',
            'jenisKelaminChart',
            'pendidikanChart',
            'pekerjaanChart',
            'allSurveyCharts'
        ));
    }

    /**
     * Download Rekap SKM in Excel format.
     */
    public function downloadRekap(Request $request)
    {
        $request->validate([
            'month' => 'required|numeric',
            'year' => 'required|numeric',
        ]);

        $bulan = $request->month;
        $tahun = $request->year;

        $namaFile = 'Rekap_SKM_' . $bulan . '-' . $tahun . '.xlsx';

        return Excel::download(new RekapSkmExport($bulan, $tahun), $namaFile);
    }
}
