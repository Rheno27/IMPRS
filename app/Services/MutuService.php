<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use App\Models\IndikatorRuangan;
use App\Models\MutuRuangan;
use Illuminate\Support\Facades\DB;

class MutuService
{
    /**
     * Menghitung statistik harian untuk daftar indikator ruangan.
     *
     * Ada DUA mode pemanggilan:
     *
     * MODE A — dari Controller (Collection):
     *   calculateDailyStats(Collection $indikators, Collection $mutuRecords)
     *   Return: array per-indikator dengan key: no, variabel, byTanggal, jumlah_total, jumlah_sesuai, persen
     *
     * MODE B — dari Unit Test (scalar ID):
     *   calculateDailyStats(int $id, int $bulan, int $tahun)
     *   Return: array date-keyed ['Y-m-d' => ['persentase' => x]]
     */
    public function calculateDailyStats($indikatorRuanganListOrId, $mutuRecordsOrBulan = null, $tahun = null): array
    {
        // =========================================================
        // MODE B: Dipanggil dari unit test dengan scalar ID
        // =========================================================
        if (!($indikatorRuanganListOrId instanceof \Illuminate\Support\Collection)) {
            $id = $indikatorRuanganListOrId;
            $bulan = $mutuRecordsOrBulan;

            $indikatorRuanganList = IndikatorRuangan::where('id_indikator_ruangan', $id)->get();
            $mutuRecords = MutuRuangan::whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $bulan)
                ->where('id_indikator_ruangan', $id)
                ->get();

            if ($mutuRecords->isEmpty()) {
                return [];
            }

            $result = [];
            foreach ($indikatorRuanganList as $item) {
                $dataMutu = $mutuRecords->filter(function ($m) use ($item) {
                    return $m->id_indikator_ruangan == $item->id_indikator_ruangan;
                });

                $groupedByDate = $dataMutu->groupBy(function ($d) {
                    return Carbon::parse($d->tanggal)->format('Y-m-d');
                });

                foreach ($groupedByDate as $tanggal => $rows) {
                    $sumSesuai = $rows->sum('pasien_sesuai');
                    $sumTotal = $rows->sum('total_pasien');
                    $pers = $sumTotal > 0 ? round($sumSesuai / $sumTotal * 100, 2) : 100;

                    $result[$tanggal] = ['persentase' => $pers];
                }
            }
            return $result;
        }

        // =========================================================
        // MODE A: Dipanggil dari Controller dengan Collection
        // =========================================================
        $indikatorRuanganList = $indikatorRuanganListOrId;
        $mutuRecords = $mutuRecordsOrBulan;

        $result = [];

        foreach ($indikatorRuanganList as $index => $item) {
            $dataMutu = $mutuRecords->filter(function ($m) use ($item) {
                return $m->id_indikator_ruangan == $item->id_indikator_ruangan;
            });

            $jumlahTotal = 0;
            $jumlahSesuai = 0;
            $byTanggal = [];

            if ($dataMutu->isNotEmpty()) {
                // Group by hari (format 'j' = 1..31) agar sesuai kebutuhan blade
                $groupedByDate = $dataMutu->groupBy(function ($d) {
                    return Carbon::parse($d->tanggal)->format('j');
                });

                foreach ($groupedByDate as $tgl => $rows) {
                    $sumSesuai = $rows->sum('pasien_sesuai');
                    $sumTotal = $rows->sum('total_pasien');

                    $byTanggal[$tgl] = (object) [
                        'pasien_sesuai' => $sumSesuai,
                        'total_pasien' => $sumTotal,
                    ];

                    $jumlahSesuai += $sumSesuai;
                    $jumlahTotal += $sumTotal;
                }
            }

            $persen = $jumlahTotal > 0 ? round(($jumlahSesuai / $jumlahTotal) * 100, 2) : 100;

            $result[] = [
                'no' => $index + 1,
                'variabel' => $item->indikatorMutu->variabel ?? 'Tanpa Judul',
                'byTanggal' => $byTanggal,
                'jumlah_total' => $jumlahTotal,
                'jumlah_sesuai' => $jumlahSesuai,
                'persen' => $persen,
            ];
        }

        return $result;
    }

    public function simpanDataMutu($userOrId, string $tanggal, $pasienSesuaiOrInt, $totalPasienOrInt): int
    {
        $updatedCount = 0;

        if (!is_object($userOrId)) {
            $id_indikator_ruangan = $userOrId;
            $nilaiSesuai = (int) $pasienSesuaiOrInt;
            $nilaiTotal = (int) $totalPasienOrInt;

            MutuRuangan::updateOrCreate(
                [
                    'tanggal' => $tanggal,
                    'id_indikator_ruangan' => $id_indikator_ruangan,
                ],
                [
                    'total_pasien' => $nilaiTotal,
                    'pasien_sesuai' => $nilaiSesuai,
                ]
            );
            return 1;
        }

        $user = $userOrId;
        $pasienSesuai = $pasienSesuaiOrInt;
        $totalPasien = $totalPasienOrInt;

        foreach ($pasienSesuai as $id_indikator => $nilaiSesuai) {
            $nilaiTotal = $totalPasien[$id_indikator] ?? 0;

            $indikatorRuangan = IndikatorRuangan::where('id_ruangan', $user->id_ruangan)
                ->where('id_indikator', $id_indikator)
                ->first();

            if (!$indikatorRuangan) {
                continue;
            }

            MutuRuangan::updateOrCreate(
                [
                    'tanggal' => $tanggal,
                    'id_indikator_ruangan' => $indikatorRuangan->id_indikator_ruangan,
                ],
                [
                    'total_pasien' => $nilaiTotal,
                    'pasien_sesuai' => $nilaiSesuai,
                ]
            );
            $updatedCount++;
        }

        return $updatedCount;
    }

    public function assignIndikatorToRuangan($id_ruangan, $id_indikator)
    {
        $existing = IndikatorRuangan::where('id_ruangan', $id_ruangan)
            ->where('id_indikator', $id_indikator)
            ->first();

        if ($existing) {
            if ($existing->active) {
                return ['status' => 'error', 'message' => 'Indikator ini sudah aktif di ruangan ini.'];
            }
            $existing->active = true;
            $existing->save();
        } else {
            IndikatorRuangan::create([
                'id_ruangan' => $id_ruangan,
                'id_indikator' => $id_indikator,
                'active' => true,
            ]);
        }
        return ['status' => 'success', 'message' => 'Indikator baru berhasil ditambahkan ke ruangan.'];
    }

    public function switchIndikatorRuangan($id_ruangan, $id_lama, $id_baru)
    {
        return DB::transaction(function () use ($id_ruangan, $id_lama, $id_baru) {
            IndikatorRuangan::where('id_indikator_ruangan', $id_lama)
                ->update(['active' => false]);

            $existingInactive = IndikatorRuangan::where('id_ruangan', $id_ruangan)
                ->where('id_indikator', $id_baru)
                ->where('active', false)
                ->first();

            if ($existingInactive) {
                $existingInactive->active = true;
                $existingInactive->save();
            } else {
                IndikatorRuangan::create([
                    'id_ruangan' => $id_ruangan,
                    'id_indikator' => $id_baru,
                    'active' => true,
                ]);
            }
            return true;
        });
    }

    public function deactivateIndikator($id_indikator_ruangan)
    {
        $logPath = storage_path('logs/mutu_debug.txt');
        file_put_contents($logPath, "deactivate called for id: {$id_indikator_ruangan}\n", FILE_APPEND);
        $item = IndikatorRuangan::findOrFail($id_indikator_ruangan);
        file_put_contents($logPath, "before active=" . var_export($item->active, true) . "\n", FILE_APPEND);
        $item->active = false;
        $item->save();
        $item->refresh();
        file_put_contents($logPath, "after active=" . var_export($item->active, true) . "\n", FILE_APPEND);
    }

    public function getSkmData($bulan, $tahun)
    {
        $maxScores = DB::table('pilihan_jawaban')
            ->select('id_pertanyaan', DB::raw('MAX(nilai) as max_nilai'))
            ->groupBy('id_pertanyaan')
            ->pluck('max_nilai', 'id_pertanyaan');

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
                    'total_pasien' => $dailyMax,
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
            'persen' => $skmPersen,
        ];
    }

    /**
     * Build chart series for a given room and year.
     */
    public function buildChartSeriesForRuangan($id_ruangan, int $tahun): array
    {
        $mutuYear = MutuRuangan::with('indikatorRuangan.indikatorMutu')
            ->whereHas('indikatorRuangan', function ($query) use ($id_ruangan) {
                $query->where('id_ruangan', $id_ruangan);
            })
            ->whereYear('tanggal', $tahun)
            ->get();

        $chartSeries = [];
        if ($mutuYear->isEmpty()) {
            return $chartSeries;
        }

        $groups = $mutuYear->groupBy(function ($item) {
            return optional($item->indikatorRuangan->indikatorMutu)->id_indikator ?? null;
        });

        foreach ($groups as $masterId => $items) {
            if ($masterId === null)
                continue;

            $label = optional($items->first()->indikatorRuangan->indikatorMutu)->variabel ?? ('Indikator ' . $masterId);

            $monthly = [];
            for ($m = 1; $m <= 12; $m++) {
                $byMonth = $items->filter(function ($it) use ($m) {
                    return (int) Carbon::parse($it->tanggal)->format('n') === $m;
                });

                if ($byMonth->isEmpty()) {
                    $monthly[] = null;
                    continue;
                }

                $sumSesuai = $byMonth->sum('pasien_sesuai');
                $sumTotal = $byMonth->sum('total_pasien');

                $monthly[] = $sumTotal > 0 ? round(($sumSesuai / $sumTotal) * 100, 2) : 100;
            }

            $kategoriName = optional($items->first()->indikatorRuangan->indikatorMutu->kategori)->kategori ?? null;

            $chartSeries[] = [
                'label' => $label,
                'monthly' => $monthly,
                'kategori' => $kategoriName,
            ];
        }

        return $chartSeries;
    }
}
