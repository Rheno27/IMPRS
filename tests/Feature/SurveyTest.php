<?php

namespace Tests\Feature;

use App\Models\BioPasien;
use App\Models\Jawaban;
use App\Models\Pertanyaan;
use App\Models\PilihanJawaban;
use App\Models\Ruangan;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SurveyTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        Ruangan::firstOrCreate(['id_ruangan' => 'R01'], ['nama_ruangan' => 'Ruangan A']);

        // Buat pertanyaan dan pilihan jawaban
        $p1 = Pertanyaan::firstOrCreate(['id_pertanyaan' => 1], ['pertanyaan' => 'Bagaimana pelayanan kami?']);
        PilihanJawaban::firstOrCreate(['id_pilihan' => 1], ['id_pertanyaan' => 1, 'pilihan' => 'Sangat Baik', 'nilai' => 4]);
        PilihanJawaban::firstOrCreate(['id_pilihan' => 2], ['id_pertanyaan' => 1, 'pilihan' => 'Baik', 'nilai' => 3]);

        $p2 = Pertanyaan::firstOrCreate(['id_pertanyaan' => 2], ['pertanyaan' => 'Kebersihan ruangan?']);
        PilihanJawaban::firstOrCreate(['id_pilihan' => 3], ['id_pertanyaan' => 2, 'pilihan' => 'Sangat Baik', 'nilai' => 4]);

        // ID 16 = pertanyaan kritik/saran (tanpa pilihan jawaban)
        Pertanyaan::firstOrCreate(['id_pertanyaan' => 16], ['pertanyaan' => 'Kritik dan Saran']);
    }

    // F37 - Halaman SKM dashboard dapat diakses publik (tanpa auth)
    public function test_skm_dashboard_is_publicly_accessible()
    {
        $response = $this->get(route('guest.dashboard'));
        $response->assertStatus(200);
    }

    // F38 - Halaman form survey dapat diakses publik
    public function test_survey_form_is_publicly_accessible()
    {
        $response = $this->get(route('guest.survei-1'));
        $response->assertStatus(200);
    }

    // F39 - Halaman survey selesai dapat diakses
    public function test_survey_done_page_is_accessible()
    {
        $response = $this->get(route('guest.survei-done'));
        $response->assertStatus(200);
    }

    // F40 - Guest bisa submit survey valid (BioPasien + Jawaban dibuat)
    public function test_guest_can_submit_valid_survey()
    {
        $response = $this->post(route('guest.survei-1.store'), [
            'id_ruangan' => 'R01',
            'no_rm' => '12345',
            'umur' => 30,
            'jenis_kelamin' => 'L',
            'pendidikan' => 'SMA',
            'pekerjaan' => 'Swasta',
            'jawaban' => [
                1 => 1,
                2 => 3,
            ],
            'kritik_saran' => 'Pelayanan sudah baik',
        ]);

        $response->assertRedirect(route('guest.survei-done'));

        $this->assertDatabaseHas('bio_pasien', [
            'no_rm' => '12345',
            'id_ruangan' => 'R01',
        ]);

        $this->assertDatabaseHas('jawaban', [
            'id_pertanyaan' => 1,
            'id_pilihan' => 1,
        ]);
    }

    // F41 - Survey gagal ketika biodata tidak lengkap
    public function test_survey_fails_when_biodata_missing()
    {
        $response = $this->post(route('guest.survei-1.store'), [
            'id_ruangan' => 'R01',
            // no_rm missing
            'jawaban' => [1 => 1],
        ]);

        $response->assertSessionHasErrors('no_rm');
    }

    // F42 - Survey gagal ketika no_rm bukan numerik
    public function test_survey_fails_when_no_rm_not_numeric()
    {
        $response = $this->post(route('guest.survei-1.store'), [
            'id_ruangan' => 'R01',
            'no_rm' => 'BUKAN-ANGKA',
            'umur' => 25,
            'jenis_kelamin' => 'P',
            'pendidikan' => 'S1',
            'pekerjaan' => 'PNS',
            'jawaban' => [1 => 1],
        ]);

        $response->assertSessionHasErrors('no_rm');
    }

    // F43 - Kritik saran disimpan sebagai Jawaban dengan id_pertanyaan = 16
    public function test_kritik_saran_stored_as_jawaban_with_id_pertanyaan_16()
    {
        $response = $this->post(route('guest.survei-1.store'), [
            'id_ruangan' => 'R01',
            'no_rm' => '99999',
            'umur' => 28,
            'jenis_kelamin' => 'L',
            'pendidikan' => 'D3',
            'pekerjaan' => 'Wiraswasta',
            'jawaban' => [1 => 1],
            'kritik_saran' => 'Tolong tingkatkan kebersihan',
        ]);

        $response->assertRedirect(route('guest.survei-done'));

        $bioPasien = BioPasien::where('no_rm', '99999')->first();
        $this->assertNotNull($bioPasien);

        $this->assertDatabaseHas('jawaban', [
            'id_pertanyaan' => 16,
            'id_pasien' => $bioPasien->id_pasien,
        ]);
    }
}
