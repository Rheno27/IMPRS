<?php

namespace Tests\Feature;

use App\Models\Jawaban;
use App\Models\Pertanyaan;
use App\Models\PilihanJawaban;
use App\Models\Ruangan;
use App\Models\User;
use App\Services\SkmService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SkmServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected SkmService $service;
    protected User $superadmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new SkmService();

        Ruangan::firstOrCreate(['id_ruangan' => 'SP00'], ['nama_ruangan' => 'Superadmin']);

        $this->superadmin = User::create([
            'id_user' => 'SP001',
            'id_ruangan' => 'SP00',
            'username' => 'superadmin',
            'password' => Hash::make('password'),
            'nama_ruangan' => 'Super Admin',
        ]);
    }

    // F48 - syncPertanyaan() membuat pertanyaan baru beserta pilihan_jawaban
    public function test_sync_pertanyaan_creates_new_questions_with_pilihan()
    {
        $data = [
            [
                'pertanyaan' => 'Pertanyaan Baru',
                'pilihan_jawaban' => [
                    ['pilihan' => 'Sangat Baik', 'nilai' => 4],
                    ['pilihan' => 'Baik', 'nilai' => 3],
                ],
            ],
        ];

        $this->service->syncPertanyaan($data);

        $this->assertDatabaseHas('pertanyaan', ['pertanyaan' => 'Pertanyaan Baru']);

        $pertanyaan = Pertanyaan::where('pertanyaan', 'Pertanyaan Baru')->first();
        $this->assertEquals(2, $pertanyaan->pilihanJawaban()->count());
    }

    // F49 - syncPertanyaan() mengupdate pertanyaan yang sudah ada (ada id_pertanyaan)
    public function test_sync_pertanyaan_updates_existing_question()
    {
        $existing = Pertanyaan::firstOrCreate(['id_pertanyaan' => 5], ['pertanyaan' => 'Pertanyaan Lama']);
        PilihanJawaban::firstOrCreate(['id_pilihan' => 1], ['id_pertanyaan' => 5, 'pilihan' => 'Baik', 'nilai' => 3]);

        $data = [
            [
                'id_pertanyaan' => 5,
                'pertanyaan' => 'Pertanyaan Diupdate',
                'pilihan_jawaban' => [
                    ['pilihan' => 'Sangat Baik', 'nilai' => 4],
                    ['pilihan' => 'Baik', 'nilai' => 3],
                ],
            ],
        ];

        $this->service->syncPertanyaan($data);

        $this->assertDatabaseHas('pertanyaan', [
            'id_pertanyaan' => 5,
            'pertanyaan' => 'Pertanyaan Diupdate',
        ]);
    }

    // F50 - syncPertanyaan() menghapus pertanyaan yang dihilangkan dari daftar
    public function test_sync_pertanyaan_deletes_removed_questions()
    {
        $toDelete = Pertanyaan::firstOrCreate(['id_pertanyaan' => 10], ['pertanyaan' => 'Akan Dihapus']);
        $toKeep = Pertanyaan::firstOrCreate(['id_pertanyaan' => 11], ['pertanyaan' => 'Akan Disimpan']);

        // Sync hanya mengirim pertanyaan ID 11, sehingga ID 10 harus dihapus
        $data = [
            [
                'id_pertanyaan' => 11,
                'pertanyaan' => 'Akan Disimpan',
                'pilihan_jawaban' => [
                    ['pilihan' => 'Baik', 'nilai' => 3],
                ],
            ],
        ];

        $this->service->syncPertanyaan($data);

        $this->assertDatabaseMissing('pertanyaan', ['id_pertanyaan' => 10]);
        $this->assertDatabaseHas('pertanyaan', ['id_pertanyaan' => 11]);
    }

    // F51 - deleteSinglePertanyaan() gagal ketika sudah ada jawaban
    public function test_delete_single_pertanyaan_fails_when_has_responses()
    {
        $pertanyaan = Pertanyaan::firstOrCreate(['id_pertanyaan' => 20], ['pertanyaan' => 'Ada Jawaban']);
        $pilihan = PilihanJawaban::firstOrCreate(['id_pilihan' => 10], ['id_pertanyaan' => 20, 'pilihan' => 'Baik', 'nilai' => 3]);

        Ruangan::firstOrCreate(['id_ruangan' => 'R01'], ['nama_ruangan' => 'Ruangan A']);
        $bioPasien = \App\Models\BioPasien::create([
            'id_pasien' => 1,
            'id_ruangan' => 'R01',
            'no_rm' => '11111',
            'umur' => 25,
            'jenis_kelamin' => 'L',
            'pendidikan' => 'SMA',
            'pekerjaan' => 'Swasta',
        ]);

        Jawaban::create([
            'id_pasien' => $bioPasien->id_pasien,
            'id_pertanyaan' => 20,
            'id_pilihan' => 10,
            'tanggal' => now()->format('Y-m-d'),
            'hasil_nilai' => 3,
        ]);

        $this->expectException(\Exception::class);
        $this->service->deleteSinglePertanyaan(20);
    }

    // F52 - deleteSinglePertanyaan() berhasil ketika tidak ada jawaban
    public function test_delete_single_pertanyaan_succeeds_when_no_responses()
    {
        Pertanyaan::firstOrCreate(['id_pertanyaan' => 21], ['pertanyaan' => 'Belum Ada Jawaban']);
        PilihanJawaban::firstOrCreate(['id_pilihan' => 11], ['id_pertanyaan' => 21, 'pilihan' => 'Baik', 'nilai' => 3]);

        $this->service->deleteSinglePertanyaan(21);

        $this->assertDatabaseMissing('pertanyaan', ['id_pertanyaan' => 21]);
        $this->assertDatabaseMissing('pilihan_jawaban', ['id_pertanyaan' => 21]);
    }
}
