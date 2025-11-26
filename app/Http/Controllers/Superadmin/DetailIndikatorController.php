<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Ruangan;
use App\Models\IndikatorRuangan;
use App\Models\MutuRuangan;
use Illuminate\Support\Facades\Session;
use App\Exports\RekapMutuRuanganExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetailIndikatorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function show(Request $request, Ruangan $ruangan)
    {
        $bulan = $request->input('bulan', date('n'));
        $tahun = $request->input('tahun', date('Y'));

        // =========================================================================
        // 1. DATA INDIKATOR RUANGAN (Logika Lama)
        // =========================================================================

        // Ambil data mutu ruangan
        $mutu = MutuRuangan::with('indikatorRuangan.indikatorMutu')
            ->whereHas('indikatorRuangan', function ($query) use ($ruangan) {
                $query->where('id_ruangan', $ruangan->id_ruangan);
            })
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();

        // Ambil daftar indikator aktif
        $indikators = IndikatorRuangan::where('id_ruangan', $ruangan->id_ruangan)
            ->where('active', true)
            ->with('indikatorMutu')
            ->get();

        $namaBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        $indikatorData = [];

        // Loop Indikator Ruangan
        foreach ($indikators as $i => $item) {
            $dataMutu = $mutu->filter(function ($m) use ($item) {
                return $m->id_indikator_ruangan == $item->id_indikator_ruangan;
            });

            $byTanggal = $dataMutu->keyBy(function ($d) {
                return Carbon::parse($d->tanggal)->format('j');
            });

            $jumlah_total = $dataMutu->sum('total_pasien');
            $jumlah_sesuai = $dataMutu->sum('pasien_sesuai');
            $persen = $jumlah_total > 0 ? round($jumlah_sesuai / $jumlah_total * 100, 2) : 0;

            $indikatorData[] = [
                'no' => $i + 1,
                'variabel' => $item->indikatorMutu->variabel,
                'byTanggal' => $byTanggal,
                'jumlah_total' => $jumlah_total,
                'jumlah_sesuai' => $jumlah_sesuai,
                'persen' => $persen,
            ];
        }

        // =========================================================================
        // 2. DATA SKM GLOBAL (Fitur Baru: Ditambahkan di Bawah Tabel)
        // =========================================================================

        // A. Ambil nilai MAX untuk setiap pertanyaan (sebagai Denominator/Pembagi)
        // Contoh: Pertanyaan 1 max nilainya 5. Pertanyaan 11 max nilainya 10.
        $maxScores = DB::table('pilihan_jawaban')
            ->select('id_pertanyaan', DB::raw('MAX(nilai) as max_nilai'))
            ->groupBy('id_pertanyaan')
            ->pluck('max_nilai', 'id_pertanyaan');

        // B. Ambil semua jawaban SKM bulan ini (Global / Semua Ruangan)
        $skmAnswers = DB::table('jawaban')
            ->join('pilihan_jawaban', 'jawaban.id_pilihan', '=', 'pilihan_jawaban.id_pilihan')
            ->select('jawaban.tanggal', 'jawaban.id_pertanyaan', 'pilihan_jawaban.nilai')
            ->whereMonth('jawaban.tanggal', $bulan)
            ->whereYear('jawaban.tanggal', $tahun)
            ->get();

        // C. Proses Data SKM per Tanggal
        $skmByTanggal = [];
        $skmTotalActual = 0;
        $skmTotalMax = 0;

        if ($skmAnswers->isNotEmpty()) {
            // Grouping berdasarkan tanggal (1, 2, ..., 31)
            $groupedSkm = $skmAnswers->groupBy(function ($item) {
                return Carbon::parse($item->tanggal)->format('j');
            });

            foreach ($groupedSkm as $tgl => $answers) {
                $dailyActual = 0;
                $dailyMax = 0;

                foreach ($answers as $ans) {
                    $dailyActual += $ans->nilai;
                    // Tambahkan nilai maksimal yang seharusnya didapat untuk pertanyaan ini
                    $dailyMax += $maxScores[$ans->id_pertanyaan] ?? 0;
                }

                // Simpan data harian format object (biar sama dengan struktur MutuRuangan)
                // pasien_sesuai = SKOR YANG DIDAPAT
                // total_pasien  = SKOR MAKSIMAL (TARGET)
                $skmByTanggal[$tgl] = (object) [
                    'pasien_sesuai' => $dailyActual,
                    'total_pasien' => $dailyMax
                ];

                $skmTotalActual += $dailyActual;
                $skmTotalMax += $dailyMax;
            }
        }

        // D. Hitung Persentase Akhir SKM
        $skmPersen = $skmTotalMax > 0 ? round(($skmTotalActual / $skmTotalMax) * 100, 2) : 0;

        // E. Masukkan ke dalam array utama sebagai baris terakhir
        $indikatorData[] = [
            'no' => count($indikatorData) + 1, // Nomor urut terakhir
            'variabel' => 'Kepuasan Masyarakat',
            'byTanggal' => $skmByTanggal,
            'jumlah_total' => $skmTotalMax,    // Ini akan jadi kolom "Total Pasien/Kejadian" (Denominator)
            'jumlah_sesuai' => $skmTotalActual, // Ini akan jadi kolom "Jumlah Sesuai" (Numerator)
            'persen' => $skmPersen
        ];

        // =========================================================================

        $jumlahHari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

        return view('superadmin.detail_indikator', [
            'ruangan' => $ruangan,
            'indikatorData' => $indikatorData,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'namaBulan' => $namaBulan,
            'jumlahHari' => $jumlahHari
        ]);
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
     * Download Rekap Mutu Ruangan in Excel Format.
     */
    public function downloadRekap(Request $request)
    {
        // 1. Cek Security: Apakah user sudah login (via Session manual)?
        if (!Session::has('user')) {
            return redirect('/login')->withErrors(['login' => 'Silakan login terlebih dahulu']);
        }

        // 2. Validasi
        $request->validate([
            'bulan' => 'required',
            'tahun' => 'required',
            'ruangan_id' => 'required' // Wajib ada untuk Superadmin
        ]);

        // 3. Proses Download
        // Kita ambil ID Ruangan dari Form, bukan dari Session User
        $ruanganId = $request->ruangan_id;
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $namaFile = 'Rekap_Mutu_' . $ruanganId . '_' . $bulan . '-' . $tahun . '.xlsx';

        return Excel::download(new RekapMutuRuanganExport($ruanganId, $bulan, $tahun), $namaFile);
    }
}
