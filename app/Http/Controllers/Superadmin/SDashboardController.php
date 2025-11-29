<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IndikatorRuangan;
use App\Models\IndikatorMutu;
use App\Models\MutuRuangan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Exports\RekapPerIndikatorExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class SDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Ambil tahun dari request, default ke tahun saat ini
        $tahun = $request->input('tahun', date('Y'));
        // Ambil kategori, default ke INM
        $selectedKategori = $request->input('kategori', 'Indikator Nasional Mutu');

        $results = collect();

        // --- LOGIKA 1: JIKA KATEGORI ADALAH IMPU (Tampilkan per Ruangan) ---
        if ($selectedKategori === 'Indikator Mutu Prioritas Unit') {

            $relevantIndicators = IndikatorRuangan::query()
                ->whereHas('indikatorMutu.kategori', function ($query) use ($selectedKategori) {
                    $query->where('kategori', $selectedKategori);
                })
                ->where('active', true)
                ->with([
                    'ruangan',
                    'indikatorMutu',
                    'mutuRuangan' => function ($query) use ($tahun) {
                        $query->whereYear('tanggal', $tahun);
                    }
                ])
                ->orderBy('id_ruangan', 'asc')
                ->get();

            $results = $relevantIndicators->map(function ($indicator) {
                $monthlyData = $indicator->mutuRuangan->groupBy(function ($mutu) {
                    return (int) date('n', strtotime($mutu->tanggal));
                });

                $monthlyAverages = $this->calculateMonthlyStats($monthlyData);

                return (object) [
                    'ruangan' => $indicator->ruangan->nama_ruangan ?? 'N/A',
                    'judul' => $indicator->indikatorMutu->variabel ?? 'N/A',
                    'standar' => $indicator->indikatorMutu->standar ?? 'N/A',
                    'data_bulan' => $monthlyAverages,
                ];
            });

        }
        // --- LOGIKA 2: JIKA KATEGORI ADALAH INM ATAU IMPRS (Gabung/Agregat) ---
        else {
            // 1. Ambil semua Indikator Master sesuai kategori
            $masterIndicators = IndikatorMutu::query()
                ->whereHas('kategori', function ($q) use ($selectedKategori) {
                    $q->where('kategori', $selectedKategori);
                })
                ->where('variabel', 'NOT LIKE', '%Kepuasan Masyarakat%')
                ->get();

            $results = $masterIndicators->map(function ($indMaster) use ($tahun) {

                $relatedIndikatorRuanganIds = IndikatorRuangan::where('id_indikator', $indMaster->id_indikator)
                    ->pluck('id_indikator_ruangan');

                $allMutuData = MutuRuangan::whereIn('id_indikator_ruangan', $relatedIndikatorRuanganIds)
                    ->whereYear('tanggal', $tahun)
                    ->get();

                $monthlyData = $allMutuData->groupBy(function ($item) {
                    return (int) date('n', strtotime($item->tanggal));
                });

                $monthlyAverages = $this->calculateMonthlyStats($monthlyData);

                return (object) [
                    'ruangan' => '-',
                    'judul' => $indMaster->variabel,
                    'standar' => $indMaster->standar,
                    'data_bulan' => $monthlyAverages,
                ];
            });

            // === TAMBAHAN: HITUNG SKM JIKA KATEGORI ADALAH INM ===
            // Ini akan menambahkan baris "Kepuasan Masyarakat" di bawah tabel INM
            if ($selectedKategori === 'Indikator Nasional Mutu') {
                $skmObject = $this->calculateGlobalSkmYearly($tahun);
                if ($skmObject) {
                    $results->push($skmObject);
                }
            }
        }

        return view('superadmin.dashboard', [
            'indikatorData' => $results,
            'selectedKategori' => $selectedKategori,
            'tahun' => $tahun,
        ]);
    }

    private function calculateGlobalSkmYearly($year)
    {
        // 1. Cari Data Indikator di DB (Untuk Judul & Standar)
        $skmIndicatorDB = DB::table('indikator_mutu')
            ->where('variabel', 'LIKE', '%Kepuasan Masyarakat%')
            ->first();

        $judulSKM = $skmIndicatorDB ? $skmIndicatorDB->variabel : 'Kepuasan Masyarakat';
        $standarSKM = $skmIndicatorDB ? $skmIndicatorDB->standar : '> 76.61';

        // 2. Ambil Nilai Max per Pertanyaan (Denominator)
        $maxScores = DB::table('pilihan_jawaban')
            ->select('id_pertanyaan', DB::raw('MAX(nilai) as max_nilai'))
            ->groupBy('id_pertanyaan')
            ->pluck('max_nilai', 'id_pertanyaan');

        // 3. Ambil Jawaban SKM Tahun Ini
        $skmAnswers = DB::table('jawaban')
            ->join('pilihan_jawaban', 'jawaban.id_pilihan', '=', 'pilihan_jawaban.id_pilihan')
            ->select('jawaban.tanggal', 'jawaban.id_pertanyaan', 'pilihan_jawaban.nilai')
            ->whereYear('jawaban.tanggal', $year)
            ->get();

        if ($skmAnswers->isEmpty()) {
            return (object) [
                'ruangan' => '-',
                'judul' => $judulSKM,
                'standar' => $standarSKM,
                'data_bulan' => array_fill(1, 12, null)
            ];
        }

        // 4. Grouping Per Bulan
        $answersByMonth = $skmAnswers->groupBy(function ($item) {
            return (int) Carbon::parse($item->tanggal)->format('n');
        });

        $monthlyStats = [];

        for ($m = 1; $m <= 12; $m++) {
            if (isset($answersByMonth[$m])) {
                $totalActual = 0;
                $totalMax = 0;

                foreach ($answersByMonth[$m] as $ans) {
                    $totalActual += $ans->nilai;
                    $totalMax += $maxScores[$ans->id_pertanyaan] ?? 0;
                }

                $monthlyStats[$m] = $totalMax > 0
                    ? round(($totalActual / $totalMax) * 100, 2) . '%'
                    : null;
            } else {
                $monthlyStats[$m] = null;
            }
        }

        return (object) [
            'ruangan' => '-',
            'judul' => $judulSKM,
            'standar' => $standarSKM,
            'data_bulan' => $monthlyStats,
        ];
    }

    /**
     * Helper function untuk menghitung persentase bulanan
     * Mencegah duplikasi kode hitungan
     */
    private function calculateMonthlyStats($groupedData)
    {
        $monthlyAverages = [];
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            if (isset($groupedData[$bulan])) {
                $dataBulanIni = $groupedData[$bulan];

                $totalSesuai = $dataBulanIni->sum('pasien_sesuai');
                $totalPasien = $dataBulanIni->sum('total_pasien');

                $persen = ($totalPasien > 0)
                    ? round(($totalSesuai / $totalPasien) * 100, 2) . '%'
                    : null;

                $monthlyAverages[$bulan] = $persen;
            } else {
                $monthlyAverages[$bulan] = null;
            }
        }
        return $monthlyAverages;
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
     * Download Rekap Indikator Mutu per Kategori in Excel Format.
     */
    public function downloadRekapIndikator(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $request->validate([
            'tahun' => 'required',
            'kategori' => 'required',
        ]);

        $namaFile = 'Rekap_' . $request->kategori . '_' . $request->tahun . '.xlsx';

        return Excel::download(new RekapPerIndikatorExport($request->kategori, $request->tahun), $namaFile);
    }
}
