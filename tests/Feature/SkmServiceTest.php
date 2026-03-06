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

    // F50 - syncPertanyaan() menghapus pertanyaan yang dihilangkan dari daftar
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

        // Sync hanya mengirim pertanyaan ID 911, sehingga ID 910 harus dihapus
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

    // F51 - deleteSinglePertanyaan() gagal ketika sudah ada jawaban
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

        Ruangan::firstOrCreate(['id_ruangan' => 'R01'], ['nama_ruangan' => 'Ruangan A']);
        $bioPasien = \App\Models\BioPasien::firstOrCreate(
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
            ['id_pilihan' => 920, 'tanggal' => now()->toDateString(), 'hasil_nilai' => 'Baik']
        );

        $this->expectException(\Exception::class);
        $this->service->deleteSinglePertanyaan(920);
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

    // U46 - syncPertanyaan() menghapus pilihan_jawaban yang terkait saat pertanyaan dihapus (cascade)
    public function test_sync_pertanyaan_cascade_deletes_related_pilihan()
    {
        // Buat pertanyaan dengan pilihan yang akan dihapus
        Pertanyaan::firstOrCreate(['id_pertanyaan' => 30], ['pertanyaan' => 'Pertanyaan Akan Dihapus', 'urutan' => 30]);
        PilihanJawaban::firstOrCreate(['id_pilihan' => 30], ['id_pertanyaan' => 30, 'pilihan' => 'Opsi A', 'nilai' => 1]);

        // Buat pertanyaan yang akan dipertahankan
        Pertanyaan::firstOrCreate(['id_pertanyaan' => 31], ['pertanyaan' => 'Pertanyaan Disimpan', 'urutan' => 31]);

        // Sync hanya mengirim id_pertanyaan = 31, sehingga id_pertanyaan = 30 + pilihannya harus hilang
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

        // Pertanyaan 30 dan pilihannya harus terhapus
        $this->assertDatabaseMissing('pertanyaan', ['id_pertanyaan' => 30]);
        $this->assertDatabaseMissing('pilihan_jawaban', ['id_pertanyaan' => 30]);
        // Pertanyaan 31 harus tetap ada
        $this->assertDatabaseHas('pertanyaan', ['id_pertanyaan' => 31]);
    }

    // F69 - Rekap SKM bisa difilter per ruangan (hanya tampilkan data ruangan tertentu)
    public function test_skm_rekap_can_filter_by_ruangan()
    {
        Ruangan::firstOrCreate(['id_ruangan' => 'R01'], ['nama_ruangan' => 'Ruangan A']);

        $response = $this->actingAs($this->superadmin)->get(route('superadmin.skm.rekap', [
            'ruangan' => 'R01',
            'month' => 1,
            'year' => 2025,
        ]));

        $response->assertStatus(200);
        // Halaman berhasil dimuat dengan filter ruangan
    }
}