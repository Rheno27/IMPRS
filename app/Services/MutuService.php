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
}