<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IndikatorRuangan;
use Illuminate\Support\Facades\DB;

class SDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Ambil tahun dari request, default ke tahun saat ini jika tidak ada
        $tahun = $request->input('tahun', date('Y'));
        // Ambil kategori, default ke INM
        $selectedKategori = $request->input('kategori', 'Indikator Nasional Mutu (INM)');

        // LANGKAH 1: Ambil semua indikator yang punya data di tahun & kategori terpilih.
        // Kita tidak lagi menggunakan where('active', true).
        $relevantIndicators = IndikatorRuangan::query()
            // Filter berdasarkan kategori yang dipilih
            ->whereHas('indikatorMutu.kategori', function ($query) use ($selectedKategori) {
                $query->where('kategori', $selectedKategori);
            })
            // PASTIKAN HANYA MENGAMBIL INDIKATOR YANG PUNYA DATA DI TAHUN TERPILIH
            ->whereHas('mutuRuangan', function ($query) use ($tahun) {
                $query->whereYear('tanggal', $tahun);
            })
            // Ambil relasi yang dibutuhkan
            ->with([
                'ruangan',
                'indikatorMutu',
                'mutuRuangan' => function ($query) use ($tahun) {
                    $query->whereYear('tanggal', $tahun);
                }
            ])
            ->orderBy('id_ruangan', 'asc')
            ->get();

        // LANGKAH 2: Proses data untuk dihitung rata-rata bulanannya
        $results = $relevantIndicators->map(function ($indicator) {
            // Kelompokkan semua data harian berdasarkan bulan (kunci 1 untuk Jan, 2 untuk Feb, dst.)
            $monthlyData = $indicator->mutuRuangan->groupBy(function ($mutu) {
                return (int) date('n', strtotime($mutu->tanggal));
            });

            $monthlyAverages = [];
            // Loop dari bulan 1 sampai 12
            for ($bulan = 1; $bulan <= 12; $bulan++) {
                // Cek apakah ada data untuk bulan ini
                if (isset($monthlyData[$bulan])) {
                    $dataBulanIni = $monthlyData[$bulan];
                    $totalSesuai = $dataBulanIni->sum('pasien_sesuai');
                    $totalPasien = $dataBulanIni->sum('total_pasien');

                    // Hitung persentase, hindari pembagian dengan nol
                    $persen = ($totalPasien > 0)
                        ? round(($totalSesuai / $totalPasien) * 100, 2)
                        : null; // Beri null jika tidak ada data pasien

                    $monthlyAverages[$bulan] = $persen;
                } else {
                    // Jika tidak ada data sama sekali untuk bulan ini, beri nilai null
                    $monthlyAverages[$bulan] = null;
                }
            }

            return (object) [
                'ruangan' => $indicator->ruangan->nama_ruangan ?? 'N/A',
                'judul' => $indicator->indikatorMutu->variabel ?? 'N/A',
                'standar' => $indicator->indikatorMutu->standar ?? 'N/A',
                'data_bulan' => $monthlyAverages, // Array berisi 12 data bulanan
            ];
        });

        // 3. Kirim data yang sudah diolah ke view.
        return view('superadmin.dashboard', [
            'indikatorData' => $results, // Ganti nama variabel agar sesuai dengan view lama
            'selectedKategori' => $selectedKategori,
            'tahun' => $tahun,
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
}
