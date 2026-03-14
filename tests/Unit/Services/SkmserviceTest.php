<?php

namespace Tests\Unit\Services;

use App\Models\BioPasien;
use App\Models\Jawaban;
use App\Models\Pertanyaan;
use App\Models\PilihanJawaban;
use App\Models\Ruangan;
use App\Services\SkmService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SkmServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected SkmService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SkmService();

        Ruangan::firstOrCreate(['id_ruangan' => 'R01'], ['nama_ruangan' => 'Ruangan A']);
    }

    // =========================================================================
    // syncPertanyaan()
    // =========================================================================

    // U69 - Membuat pertanyaan baru beserta pilihan_jawaban
    public function test_sync_pertanyaan_creates_new_questions_with_pilihan()
    {
        $data = [
            [
                'pertanyaan' => 'Pertanyaan Baru Test',
                'pilihan_jawaban' => [
                    ['pilihan' => 'Sangat Baik', 'nilai' => 4],
                    ['pilihan' => 'Baik', 'nilai' => 3],
                ],
            ],
        ];

        $this->service->syncPertanyaan($data);

        $this->assertDatabaseHas('pertanyaan', ['pertanyaan' => 'Pertanyaan Baru Test']);

        $pertanyaan = Pertanyaan::where('pertanyaan', 'Pertanyaan Baru Test')->first();
        $this->assertEquals(2, $pertanyaan->pilihanJawaban()->count());
    }

    // U70 - Mengupdate pertanyaan yang sudah ada (ada id_pertanyaan)
    public function test_sync_pertanyaan_updates_existing_question()
    {
        Pertanyaan::firstOrCreate(
            ['id_pertanyaan' => 901],
            ['pertanyaan' => 'Pertanyaan Lama', 'urutan' => 901]
        );
        PilihanJawaban::firstOrCreate(
            ['id_pilihan' => 901],
            ['id_pertanyaan' => 901, 'pilihan' => 'Baik', 'nilai' => 3]
        );

        $data = [
            [
                'id_pertanyaan' => 901,
                'pertanyaan' => 'Pertanyaan Diupdate',
                'pilihan_jawaban' => [
                    ['pilihan' => 'Sangat Baik', 'nilai' => 4],
                    ['pilihan' => 'Baik', 'nilai' => 3],
                ],
            ],
        ];

        $this->service->syncPertanyaan($data);

        $this->assertDatabaseHas('pertanyaan', [
            'id_pertanyaan' => 901,
            'pertanyaan' => 'Pertanyaan Diupdate',
        ]);
    }

    // U71 - Menghapus pertanyaan yang dihilangkan dari daftar
    public function test_sync_pertanyaan_deletes_removed_questions()
    {
        Pertanyaan::firstOrCreate(
            ['id_pertanyaan' => 910],
            ['pertanyaan' => 'Akan Dihapus', 'urutan' => 910]
        );
        Pertanyaan::firstOrCreate(
            ['id_pertanyaan' => 911],
            ['pertanyaan' => 'Akan Disimpan', 'urutan' => 911]
        );

        // Sync hanya mengirim ID 911 → ID 910 harus terhapus
        $data = [
            [
                'id_pertanyaan' => 911,
                'pertanyaan' => 'Akan Disimpan',
                'pilihan_jawaban' => [
                    ['pilihan' => 'Baik', 'nilai' => 3],
                ],
            ],
        ];

        $this->service->syncPertanyaan($data);

        $this->assertDatabaseMissing('pertanyaan', ['id_pertanyaan' => 910]);
        $this->assertDatabaseHas('pertanyaan', ['id_pertanyaan' => 911]);
    }

    // U72 - Cascade delete pilihan_jawaban saat pertanyaan dihapus
    public function test_sync_pertanyaan_cascade_deletes_related_pilihan()
    {
        Pertanyaan::firstOrCreate(
            ['id_pertanyaan' => 30],
            ['pertanyaan' => 'Pertanyaan Akan Dihapus', 'urutan' => 30]
        );
        PilihanJawaban::firstOrCreate(
            ['id_pilihan' => 30],
            ['id_pertanyaan' => 30, 'pilihan' => 'Opsi A', 'nilai' => 1]
        );
        Pertanyaan::firstOrCreate(
            ['id_pertanyaan' => 31],
            ['pertanyaan' => 'Pertanyaan Disimpan', 'urutan' => 31]
        );

        $data = [
            [
                'id_pertanyaan' => 31,
                'pertanyaan' => 'Pertanyaan Disimpan',
                'pilihan_jawaban' => [
                    ['pilihan' => 'Baik', 'nilai' => 3],
                ],
            ],
        ];

        $this->service->syncPertanyaan($data);

        // Pertanyaan 30 dan pilihannya terhapus
        $this->assertDatabaseMissing('pertanyaan', ['id_pertanyaan' => 30]);
        $this->assertDatabaseMissing('pilihan_jawaban', ['id_pertanyaan' => 30]);
        // Pertanyaan 31 tetap ada
        $this->assertDatabaseHas('pertanyaan', ['id_pertanyaan' => 31]);
    }

    // =========================================================================
    // deleteSinglePertanyaan()
    // =========================================================================

    // U73 - Gagal dan throw Exception jika sudah ada jawaban
    public function test_delete_single_pertanyaan_fails_when_has_responses()
    {
        Pertanyaan::firstOrCreate(
            ['id_pertanyaan' => 920],
            ['pertanyaan' => 'Ada Jawaban', 'urutan' => 920]
        );
        PilihanJawaban::firstOrCreate(
            ['id_pilihan' => 920],
            ['id_pertanyaan' => 920, 'pilihan' => 'Baik', 'nilai' => 3]
        );

        $bioPasien = BioPasien::firstOrCreate(
            ['no_rm' => '99999'],
            [
                'id_ruangan' => 'R01',
                'umur' => 25,
                'jenis_kelamin' => 'L',
                'pendidikan' => 'SMA',
                'pekerjaan' => 'Swasta',
            ]
        );

        Jawaban::firstOrCreate(
            ['id_pasien' => $bioPasien->id_pasien, 'id_pertanyaan' => 920],
            [
                'id_pilihan' => 920,
                'tanggal' => now()->toDateString(),
                'hasil_nilai' => 'Baik',
            ]
        );

        $this->expectException(\Exception::class);
        $this->service->deleteSinglePertanyaan(920);
    }

    // U74 - Berhasil hapus pertanyaan + pilihannya jika belum ada jawaban
    public function test_delete_single_pertanyaan_succeeds_when_no_responses()
    {
        Pertanyaan::firstOrCreate(
            ['id_pertanyaan' => 21],
            ['pertanyaan' => 'Belum Ada Jawaban', 'urutan' => 21]
        );
        PilihanJawaban::firstOrCreate(
            ['id_pilihan' => 11],
            ['id_pertanyaan' => 21, 'pilihan' => 'Baik', 'nilai' => 3]
        );

        $this->service->deleteSinglePertanyaan(21);

        $this->assertDatabaseMissing('pertanyaan', ['id_pertanyaan' => 21]);
        $this->assertDatabaseMissing('pilihan_jawaban', ['id_pertanyaan' => 21]);
    }
}