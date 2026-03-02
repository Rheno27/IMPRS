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
     * Digunakan di Admin Dashboard dan Superadmin Detail Indikator.
     *
     * @param Collection $indikatorRuanganList (Daftar IndikatorRuangan)
     * @param Collection $mutuRecords (Data MutuRuangan pada bulan tersebut)
     * @return array
     */
    public function calculateDailyStats(Collection $indikatorRuanganList, Collection $mutuRecords): array
    {
        $result = [];

        foreach ($indikatorRuanganList as $index => $item) {
            $dataMutu = $mutuRecords->filter(function ($m) use ($item) {
                return $m->id_indikator_ruangan == $item->id_indikator_ruangan;
            });

            $byTanggal = $dataMutu->keyBy(function ($d) {
                return Carbon::parse($d->tanggal)->format('j');
            });

            $jumlah_total = $dataMutu->sum('total_pasien');
            $jumlah_sesuai = $dataMutu->sum('pasien_sesuai');
            $persen = $jumlah_total > 0 ? round($jumlah_sesuai / $jumlah_total * 100, 2) : 0;

            $result[] = [
                'no' => $index + 1,
                'variabel' => $item->indikatorMutu->variabel ?? 'Tanpa Judul', // Safe access
                'byTanggal' => $byTanggal,
                'jumlah_total' => $jumlah_total,
                'jumlah_sesuai' => $jumlah_sesuai,
                'persen' => $persen,
            ];
        }

        return $result;
    }

    public function simpanDataMutu($user, string $tanggal, array $pasienSesuai, array $totalPasien): int
    {
        $updatedCount = 0;

        foreach ($pasienSesuai as $id_indikator => $nilaiSesuai) {
            $nilaiTotal = $totalPasien[$id_indikator] ?? 0;

            // Pastikan indikator milik ruangan user
            $indikatorRuangan = IndikatorRuangan::where('id_ruangan', $user->id_ruangan)
                ->where('id_indikator', $id_indikator)
                ->first();

            if (!$indikatorRuangan) {
                continue;
            }

            // Proses Simpan/Update
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
        $item = IndikatorRuangan::findOrFail($id_indikator_ruangan);
        $item->active = false;
        $item->save();
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

    /**
     * Build chart series for a given room and year.
     * Returns array of ['label' => string, 'monthly' => array(12)] where months without data are null.
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
                    return (int) \Carbon\Carbon::parse($it->tanggal)->format('n') === $m;
                });

                if ($byMonth->isEmpty()) {
                    $monthly[] = null;
                    continue;
                }

                $sumSesuai = $byMonth->sum('pasien_sesuai');
                $sumTotal = $byMonth->sum('total_pasien');

                if ($sumTotal <= 0) {
                    $monthly[] = null;
                } else {
                    $monthly[] = round(($sumSesuai / $sumTotal) * 100, 2);
                }
            }

            // attempt to read kategori name from indikatorMutu relation
            $kategoriName = optional($items->first()->indikatorRuangan->indikatorMutu->kategori)->kategori ?? null;

            $chartSeries[] = [
                'label' => $label,
                'monthly' => $monthly,
                'kategori' => $kategoriName
            ];
        }

        return $chartSeries;
    }
}