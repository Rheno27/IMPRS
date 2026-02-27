<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Ruangan;
use App\Models\IndikatorRuangan;
use App\Models\MutuRuangan;
use Illuminate\Support\Facades\Auth;
use App\Exports\RekapMutuRuanganExport;
use App\Services\MutuService;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\PilihanJawaban;
use App\Models\Jawaban;
use Illuminate\Support\Facades\DB;

class DetailIndikatorController extends Controller
{
    protected $mutuService;

    public function __construct(MutuService $mutuService)
    {
        $this->mutuService = $mutuService;
    }

    public function show(Request $request, Ruangan $ruangan)
    {
        $bulan = (int) $request->input('bulan', date('n'));
        $tahun = (int) $request->input('tahun', date('Y'));

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

        $indikatorData = $this->mutuService->calculateDailyStats($indikators, $mutu);

        $skmData = $this->getSkmData($bulan, $tahun);

        $indikatorData[] = array_merge(
            ['no' => count($indikatorData) + 1],
            $skmData
        );

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

        return view('superadmin.ruangan.detail', [
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
        $maxScores = PilihanJawaban::select('id_pertanyaan', DB::raw('MAX(nilai) as max_nilai'))
            ->groupBy('id_pertanyaan')
            ->pluck('max_nilai', 'id_pertanyaan');

        $skmAnswers = Jawaban::with('pilihanJawaban')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
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
                    $nilai = $ans->pilihanJawaban ? $ans->pilihanJawaban->nilai : 0;

                    $dailyActual += $nilai;
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