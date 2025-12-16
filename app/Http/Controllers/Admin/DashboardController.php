<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\MutuRuangan;
use App\Models\IndikatorRuangan;
use App\Exports\RekapMutuRuanganExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Services\MutuService;

class DashboardController extends Controller
{
    protected $mutuService;

    public function __construct(MutuService $mutuService)
    {
        $this->mutuService = $mutuService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $id_ruangan = $user->id_ruangan;

        $bulan = (int) $request->input('bulan', date('n'));
        $tahun = (int) $request->input('tahun', date('Y'));

        // Ambil Indikator Aktif
        $indikators = IndikatorRuangan::where('id_ruangan', $id_ruangan)
            ->where('active', true)
            ->with('indikatorMutu')
            ->get();

        // Ambil Data Mutu Bulan Ini
        $mutu = MutuRuangan::whereHas('indikatorRuangan', function ($query) use ($id_ruangan) {
            $query->where('id_ruangan', $id_ruangan);
        })
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();

        $indikatorData = $this->mutuService->calculateDailyStats($indikators, $mutu);

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
        $jumlahHari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

        return view('admin.dashboard', compact(
            'user',
            'indikatorData',
            'bulan',
            'tahun',
            'namaBulan',
            'jumlahHari'
        ));
    }

    public function downloadRekap(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'bulan' => 'required',
            'tahun' => 'required',
        ]);
        $ruanganId = $user->id_ruangan;
        $namaFile = 'Rekap_Mutu_' . $ruanganId . '_' . $request->bulan . '-' . $request->tahun . '.xlsx';
        return Excel::download(new RekapMutuRuanganExport($ruanganId, $request->bulan, $request->tahun), $namaFile);
    }
}