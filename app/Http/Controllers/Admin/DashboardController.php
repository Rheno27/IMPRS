<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\MutuRuangan;
use App\Models\IndikatorRuangan;
use App\Exports\RekapMutuRuanganExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $id_ruangan = $user->id_ruangan;

        $bulan = $request->input('bulan', date('n'));
        $tahun = $request->input('tahun', date('Y'));

        // 1. LOGIKA BARU: Ambil dulu DAFTAR INDIKATOR yang aktif untuk ruangan ini
        $indikators = IndikatorRuangan::where('id_ruangan', $id_ruangan)
            ->where('active', true)
            ->with('indikatorMutu')
            ->get();

        // 2. Setelah itu, baru ambil data mutu yang relevan untuk periode ini
        $mutu = MutuRuangan::whereHas('indikatorRuangan', function ($query) use ($id_ruangan) {
            $query->where('id_ruangan', $id_ruangan);
        })
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
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

        // 3. Proses data dengan melakukan loop dari DAFTAR INDIKATOR (bukan dari data mutu)
        $indikatorData = [];
        foreach ($indikators as $i => $item) {
            // Filter data mutu yang cocok untuk indikator ini
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

        $jumlahHari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

        return view('admin.dashboard',  compact(
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
