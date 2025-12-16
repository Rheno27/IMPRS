<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Exception;

class SkmService
{
    /**
     * Menangani logika simpan, update, dan hapus struktur pertanyaan & jawaban SKM.
     * * @param array $submittedQuestions Array dari request input('questions')
     * @return void
     * @throws Exception 
     */
    public function syncPertanyaan(array $submittedQuestions)
    {
        DB::transaction(function () use ($submittedQuestions) {
            $safePertanyaanIds = [];
            $safePilihanIds = [];

            foreach ($submittedQuestions as $index => $qData) {
                $pertanyaanData = [
                    'pertanyaan' => $qData['pertanyaan'] ?? 'Pertanyaan Kosong',
                    'urutan' => $index + 1
                ];

                if (!empty($qData['id_pertanyaan'])) {
                    $pertanyaanId = $qData['id_pertanyaan'];
                    DB::table('pertanyaan')->where('id_pertanyaan', $pertanyaanId)->update($pertanyaanData);
                } else {
                    $pertanyaanId = DB::table('pertanyaan')->insertGetId($pertanyaanData);
                }
                $safePertanyaanIds[] = $pertanyaanId;

                if (isset($qData['pilihan']) && is_array($qData['pilihan'])) {
                    foreach ($qData['pilihan'] as $pData) {
                        $pilihanData = [
                            'id_pertanyaan' => $pertanyaanId,
                            'pilihan' => $pData['pilihan'] ?? '',
                            'nilai' => isset($pData['nilai']) ? intval($pData['nilai']) : 0
                        ];

                        if (!empty($pData['id_pilihan'])) {
                            DB::table('pilihan_jawaban')->where('id_pilihan', $pData['id_pilihan'])->update($pilihanData);
                            $pilihanId = $pData['id_pilihan'];
                        } else {
                            $pilihanId = DB::table('pilihan_jawaban')->insertGetId($pilihanData);
                        }
                        $safePilihanIds[] = $pilihanId;
                    }
                }
            }

            $existingPilihanIds = DB::table('pilihan_jawaban')
                ->whereIn('id_pertanyaan', $safePertanyaanIds)
                ->pluck('id_pilihan');

            $pilihanIdsToDelete = $existingPilihanIds->diff($safePilihanIds);

            if ($pilihanIdsToDelete->isNotEmpty()) {
                DB::table('pilihan_jawaban')->whereIn('id_pilihan', $pilihanIdsToDelete)->delete();
            }

            $questionsToDelete = DB::table('pertanyaan')
                ->whereNotIn('id_pertanyaan', $safePertanyaanIds)
                ->pluck('id_pertanyaan');

            if ($questionsToDelete->isNotEmpty()) {
                foreach ($questionsToDelete as $delId) {
                    DB::table('jawaban')->where('id_pertanyaan', $delId)->delete();
                    DB::table('pilihan_jawaban')->where('id_pertanyaan', $delId)->delete();
                    DB::table('pertanyaan')->where('id_pertanyaan', $delId)->delete();
                }
            }
        });
    }

    public function deleteSinglePertanyaan($id)
    {
        $cekResponden = DB::table('jawaban')->where('id_pertanyaan', $id)->exists();

        if ($cekResponden) {
            throw new Exception('GAGAL: Pertanyaan tidak bisa dihapus karena sudah memiliki data responden. Data aman.');
        }

        DB::transaction(function () use ($id) {
            DB::table('pilihan_jawaban')->where('id_pertanyaan', $id)->delete();
            DB::table('pertanyaan')->where('id_pertanyaan', $id)->delete();
        });
    }
}