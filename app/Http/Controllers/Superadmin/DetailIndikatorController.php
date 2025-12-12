<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Ruangan;
use App\Models\IndikatorRuangan;
use App\Models\MutuRuangan;
use Illuminate\Support\Facades\Auth;
use App\Exports\RekapMutuRuanganExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetailIndikatorController extends Controller
{
    public function show(Request $request, Ruangan $ruangan)
    {
        $bulan = (int) $request->input('bulan', date('n'));
        $tahun = (int) $request->input('tahun', date('Y'));

        // 1. DATA INDIKATOR RUANGAN
        $mutu = MutuRuangan::with('indikatorRuangan.indikatorMutu')
            ->whereHas('indikatorRuangan', function ($query) use ($ruangan) {
                $query->where('id_ruangan', $ruangan->id_ruangan);
            })
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();

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

        // 2. DATA SKM GLOBAL (Panggil fungsi private)
        $skmData = $this->getSkmData($bulan, $tahun);

        // Gabungkan nomor urut + data SKM
        $indikatorData[] = array_merge(
            ['no' => count($indikatorData) + 1],
            $skmData
        );

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

    public function downloadRekap(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $request->validate([
            'bulan' => 'required',
            'tahun' => 'required',
            'ruangan_id' => 'required' 
        ]);

        $ruanganId = $request->ruangan_id;
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $namaFile = 'Rekap_Mutu_' . $ruanganId . '_' . $bulan . '-' . $tahun . '.xlsx';

        return Excel::download(new RekapMutuRuanganExport($ruanganId, $bulan, $tahun), $namaFile);
    }

    private function getSkmData($bulan, $tahun)
    {
        // Ambil nilai MAX untuk setiap pertanyaan
        $maxScores = DB::table('pilihan_jawaban')
            ->select('id_pertanyaan', DB::raw('MAX(nilai) as max_nilai'))
            ->groupBy('id_pertanyaan')
            ->pluck('max_nilai', 'id_pertanyaan');

        // Ambil semua jawaban SKM bulan ini
        $skmAnswers = DB::table('jawaban')
            ->join('pilihan_jawaban', 'jawaban.id_pilihan', '=', 'pilihan_jawaban.id_pilihan')
            ->select('jawaban.tanggal', 'jawaban.id_pertanyaan', 'pilihan_jawaban.nilai')
            ->whereMonth('jawaban.tanggal', $bulan)
            ->whereYear('jawaban.tanggal', $tahun)
            ->get();

        $skmByTanggal = [];
        $skmTotalActual = 0;
        $skmTotalMax = 0;

        if ($skmAnswers->isNotEmpty()) {
            $groupedSkm = $skmAnswers->groupBy(function ($item) {
                return Carbon::parse($item->tanggal)->format('j');
            });

            foreach ($groupedSkm as $tgl => $answers) {
                $dailyActual = 0;
                $dailyMax = 0;

                foreach ($answers as $ans) {
                    $dailyActual += $ans->nilai;
                    $dailyMax += $maxScores[$ans->id_pertanyaan] ?? 0;
                }

                $skmByTanggal[$tgl] = (object) [
                    'pasien_sesuai' => $dailyActual,
                    'total_pasien' => $dailyMax
                ];

                $skmTotalActual += $dailyActual;
                $skmTotalMax += $dailyMax;
            }
        }

        $skmPersen = $skmTotalMax > 0 ? round(($skmTotalActual / $skmTotalMax) * 100, 2) : 0;

        return [
            'variabel' => 'Kepuasan Masyarakat',
            'byTanggal' => $skmByTanggal,
            'jumlah_total' => $skmTotalMax,
            'jumlah_sesuai' => $skmTotalActual,
            'persen' => $skmPersen
        ];
    }
}
