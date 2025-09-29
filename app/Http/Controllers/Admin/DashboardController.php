<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Models\MutuRuangan;
use App\Models\IndikatorMutu;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Session::get('user');
        if (!$user)
            return redirect('/login');

        $id_ruangan = $user->id_ruangan;

        $bulan = $request->input('bulan', date('n')); // 1-12
        $tahun = $request->input('tahun', date('Y'));

        // Filter data mutu_ruangan sesuai bulan & tahun
        $mutu = MutuRuangan::where('id_ruangan', $id_ruangan)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();

        // Ambil indikator yang ada di mutu_ruangan bulan ini
        $indikatorIds = $mutu->pluck('id_indikator')->unique();
        $indikator = IndikatorMutu::whereIn('id_indikator', $indikatorIds)->get();

        // Daftar nama bulan
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

        // Di DashboardController@index
        $indikatorData = [];
        foreach ($indikator as $i => $item) {
            $dataMutu = $mutu->where('id_indikator', $item->id_indikator);
            $byTanggal = $dataMutu->keyBy(function ($d) {
                return \Carbon\Carbon::parse($d->tanggal)->format('j');
            });
            $jumlah_total = $dataMutu->sum('total_pasien');
            $jumlah_sesuai = $dataMutu->sum('pasien_sesuai');
            $persen = $jumlah_total > 0 ? round($jumlah_sesuai / $jumlah_total * 100, 2) : 0;

            $indikatorData[] = [
                'no' => $i + 1,
                'variabel' => $item->variabel,
                'byTanggal' => $byTanggal,
                'jumlah_total' => $jumlah_total,
                'jumlah_sesuai' => $jumlah_sesuai,
                'persen' => $persen,
            ];
        }

        // Hitung jumlah hari di bulan yang dipilih
        $jumlahHari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

        return view('admin.dashboard', compact(
            'indikatorData',
            'bulan',
            'tahun',
            'namaBulan',
            'jumlahHari'
        ));
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
