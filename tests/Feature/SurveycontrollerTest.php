<?php

namespace Tests\Feature\Guest;

use App\Models\BioPasien;
use App\Models\Jawaban;
use App\Models\Kategori;
use App\Models\Pertanyaan;
use App\Models\PilihanJawaban;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SurveyControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superadmin;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Ruangan::firstOrCreate(['id_ruangan' => 'SP00'], ['nama_ruangan' => 'Super Admin']);
        Ruangan::firstOrCreate(['id_ruangan' => 'R01'], ['nama_ruangan' => 'Ruangan ICU']);
        Ruangan::firstOrCreate(['id_ruangan' => 'R02'], ['nama_ruangan' => 'Ruangan Nifas']);
        Kategori::firstOrCreate(['id_kategori' => 1], ['kategori' => 'Indikator Nasional Mutu']);

        // Struktur pertanyaan + pilihan jawaban
        Pertanyaan::firstOrCreate(
            ['id_pertanyaan' => 1],
            ['pertanyaan' => 'Bagaimana pelayanan kami?', 'urutan' => 1]
        );
        PilihanJawaban::firstOrCreate(
            ['id_pilihan' => 1],
            ['id_pertanyaan' => 1, 'pilihan' => 'Sangat Baik', 'nilai' => 4]
        );
        PilihanJawaban::firstOrCreate(
            ['id_pilihan' => 2],
            ['id_pertanyaan' => 1, 'pilihan' => 'Baik', 'nilai' => 3]
        );
        Pertanyaan::firstOrCreate(
            ['id_pertanyaan' => 2],
            ['pertanyaan' => 'Kebersihan ruangan?', 'urutan' => 2]
        );
        PilihanJawaban::firstOrCreate(
            ['id_pilihan' => 3],
            ['id_pertanyaan' => 2, 'pilihan' => 'Sangat Baik', 'nilai' => 4]
        );
        // Pertanyaan kritik/saran
        Pertanyaan::firstOrCreate(
            ['id_pertanyaan' => 16],
            ['pertanyaan' => 'Kritik dan Saran', 'urutan' => 16]
        );

        // Data responden untuk test SKM superadmin
        $pasienR01 = BioPasien::create([
            'id_ruangan' => 'R01',
            'no_rm' => '11111',
            'umur' => 30,
            'jenis_kelamin' => 'L',
            'pendidikan' => 'SMA',
            'pekerjaan' => 'Swasta',
        ]);
        Jawaban::create([
            'tanggal' => '2025-01-10',
            'id_pasien' => $pasienR01->id_pasien,
            'id_pertanyaan' => 1,
            'id_pilihan' => 1,
            'hasil_nilai' => 4,
        ]);

        $pasienR02 = BioPasien::create([
            'id_ruangan' => 'R02',
            'no_rm' => '22222',
            'umur' => 25,
            'jenis_kelamin' => 'P',
            'pendidikan' => 'D3',
            'pekerjaan' => 'PNS',
        ]);
        Jawaban::create([
            'tanggal' => '2025-01-12',
            'id_pasien' => $pasienR02->id_pasien,
            'id_pertanyaan' => 1,
            'id_pilihan' => 2,
            'hasil_nilai' => 3,
        ]);

        $this->superadmin = User::create([
            'id_user' => 'SP001',
            'id_ruangan' => 'SP00',
            'username' => 'superadmin_survey',
            'password' => Hash::make('password'),
            'nama_ruangan' => 'Super Admin',
        ]);

        $this->admin = User::create([
            'id_user' => 'U001',
            'id_ruangan' => 'R01',
            'username' => 'admin_survey',
            'password' => Hash::make('password'),
            'nama_ruangan' => 'Ruangan ICU',
        ]);
    }

    // =========================================================================
    // Guest\SurveyController@create
    // =========================================================================

    // F37 - Halaman guest dashboard dapat diakses publik (200)
    public function test_guest_dashboard_is_publicly_accessible()
    {
        $response = $this->get(route('guest.dashboard'));
        $response->assertStatus(200);
    }

    // F38 - Halaman form survey dapat diakses publik (200)
    public function test_survey_form_is_publicly_accessible()
    {
        $response = $this->get(route('guest.survei-1'));
        $response->assertStatus(200);
    }

    // F39 - Halaman survey selesai dapat diakses (200)
    public function test_survey_done_page_is_accessible()
    {
        $response = $this->get(route('guest.survei-done'));
        $response->assertStatus(200);
    }

    // =========================================================================
    // Guest\SurveyController@store
    // =========================================================================

    // F40 - Guest bisa submit survey valid → BioPasien + Jawaban tersimpan di DB
    public function test_guest_can_submit_valid_survey()
    {
        $response = $this->post(route('guest.survei-1.store'), [
            'id_ruangan' => 'R01',
            'no_rm' => '12345',
            'umur' => 30,
            'jenis_kelamin' => 'L',
            'pendidikan' => 'SMA',
            'pekerjaan' => 'Swasta',
            'jawaban' => [1 => 1, 2 => 3],
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

    // F41 - Survey gagal jika biodata no_rm tidak ada → sessionHasErrors('no_rm')
    public function test_survey_fails_when_no_rm_missing()
    {
        $response = $this->post(route('guest.survei-1.store'), [
            'id_ruangan' => 'R01',
            'jawaban' => [1 => 1],
        ]);

        $response->assertSessionHasErrors('no_rm');
    }

    // F42 - Survey gagal jika no_rm bukan numerik → sessionHasErrors('no_rm')
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

    // F-GAP-9 - Survey gagal jika array jawaban tidak dikirim → sessionHasErrors('jawaban')
    public function test_survey_fails_when_jawaban_array_not_sent()
    {
        $response = $this->post(route('guest.survei-1.store'), [
            'id_ruangan' => 'R01',
            'no_rm' => '55555',
            'umur' => 30,
            'jenis_kelamin' => 'L',
            'pendidikan' => 'SMA',
            'pekerjaan' => 'Swasta',
            // jawaban sengaja tidak dikirim
        ]);

        $response->assertSessionHasErrors('jawaban');
    }

    // F-GAP-10 - Survey gagal jika id_ruangan tidak dikirim → sessionHasErrors('id_ruangan')
    public function test_survey_fails_when_id_ruangan_not_sent()
    {
        $response = $this->post(route('guest.survei-1.store'), [
            // id_ruangan sengaja tidak dikirim
            'no_rm' => '55556',
            'umur' => 30,
            'jenis_kelamin' => 'L',
            'pendidikan' => 'SMA',
            'pekerjaan' => 'Swasta',
            'jawaban' => [1 => 1],
        ]);

        $response->assertSessionHasErrors('id_ruangan');
    }

    // =========================================================================
    // SkmController@hasil
    // =========================================================================

    // F71 - Superadmin membuka halaman hasil SKM (200)
    public function test_superadmin_can_view_hasil_skm()
    {
        $response = $this->actingAs($this->superadmin)->get(route('superadmin.skm.hasil'));
        $response->assertStatus(200);
    }

    // F72 - View memiliki variabel yang dibutuhkan
    public function test_hasil_skm_passes_required_variables_to_view()
    {
        $response = $this->actingAs($this->superadmin)->get(route('superadmin.skm.hasil'));

        $response->assertStatus(200);
        $response->assertViewHas('totalResponden');
        $response->assertViewHas('allSurveyCharts');
        $response->assertViewHas('listRuangan');
        $response->assertViewHas('selectedYear');
        $response->assertViewHas('selectedMonth');
        $response->assertViewHas('ruanganChart');
        $response->assertViewHas('jenisKelaminChart');
    }

    // F73 - totalResponden terhitung benar (≥2 dari setup data)
    public function test_hasil_skm_counts_total_responden_correctly()
    {
        $response = $this->actingAs($this->superadmin)->get(route('superadmin.skm.hasil', [
            'month' => 1,
            'year' => 2025,
        ]));

        $response->assertStatus(200);
        $totalResponden = $response->viewData('totalResponden');
        $this->assertGreaterThanOrEqual(2, $totalResponden);
    }

    // F74 - Menerima filter ?year= dan ?month= → view punya nilai yang sesuai
    public function test_hasil_skm_accepts_year_month_filter()
    {
        $response = $this->actingAs($this->superadmin)->get(route('superadmin.skm.hasil', [
            'year' => 2025,
            'month' => 1,
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('selectedYear', '2025');
        $response->assertViewHas('selectedMonth', '1');
    }

    // F75 - Filter ?ruangan=R01 → totalResponden = 1
    public function test_hasil_skm_can_filter_by_ruangan()
    {
        $response = $this->actingAs($this->superadmin)->get(route('superadmin.skm.hasil', [
            'year' => 2025,
            'month' => 1,
            'ruangan' => 'R01',
        ]));

        $response->assertStatus(200);
        $totalResponden = $response->viewData('totalResponden');
        $this->assertEquals(1, $totalResponden);
    }

    // F76 - Admin tidak bisa akses hasil SKM (403)
    public function test_admin_cannot_access_hasil_skm()
    {
        $response = $this->actingAs($this->admin)->get(route('superadmin.skm.hasil'));
        $response->assertStatus(403);
    }

    // F77 - Guest redirect login dari hasil SKM
    public function test_guest_cannot_access_hasil_skm()
    {
        $response = $this->get(route('superadmin.skm.hasil'));
        $response->assertRedirect(route('login'));
    }

    // =========================================================================
    // SkmController@editPertanyaan + updatePertanyaan + destroyPertanyaan
    // =========================================================================

    // F78 - Superadmin membuka halaman edit pertanyaan SKM (200)
    public function test_superadmin_can_view_edit_pertanyaan_skm()
    {
        $response = $this->actingAs($this->superadmin)->get(route('superadmin.skm.edit2'));
        $response->assertStatus(200);
    }

    // F79 - View memiliki surveyData berisi koleksi pertanyaan (count > 0)
    public function test_edit_pertanyaan_passes_survey_data_to_view()
    {
        $response = $this->actingAs($this->superadmin)->get(route('superadmin.skm.edit2'));

        $response->assertStatus(200);
        $response->assertViewHas('surveyData');
        $surveyData = $response->viewData('surveyData');
        $this->assertGreaterThan(0, $surveyData->count());
    }

    // F80 - Admin tidak bisa membuka halaman edit pertanyaan SKM (403)
    public function test_admin_cannot_access_edit_pertanyaan_skm()
    {
        $response = $this->actingAs($this->admin)->get(route('superadmin.skm.edit2'));
        $response->assertStatus(403);
    }

    // =========================================================================
    // SkmController@downloadRekap
    // =========================================================================

    // F81 - Superadmin download rekap SKM → Excel 200
    public function test_superadmin_can_download_rekap_skm()
    {
        $response = $this->actingAs($this->superadmin)->get(route('superadmin.skm.download', [
            'month' => 1,
            'year' => 2025,
        ]));

        $response->assertStatus(200);
        $contentType = $response->headers->get('Content-Type');
        $this->assertTrue(
            str_contains($contentType, 'spreadsheet') ||
            str_contains($contentType, 'excel') ||
            str_contains($contentType, 'octet-stream'),
            "Expected Excel Content-Type, got: {$contentType}"
        );
    }

    // F82 - Download rekap SKM dapat difilter per ruangan
    public function test_superadmin_can_download_rekap_skm_filtered_by_ruangan()
    {
        $response = $this->actingAs($this->superadmin)->get(route('superadmin.skm.download', [
            'month' => 1,
            'year' => 2025,
            'ruangan' => 'R01',
        ]));

        $response->assertStatus(200);
        $contentType = $response->headers->get('Content-Type');
        $this->assertTrue(
            str_contains($contentType, 'spreadsheet') ||
            str_contains($contentType, 'excel') ||
            str_contains($contentType, 'octet-stream')
        );
    }

    // F83 - Download gagal jika month tidak dikirim → redirect/422
    public function test_download_rekap_skm_fails_without_month()
    {
        $response = $this->actingAs($this->superadmin)->get(route('superadmin.skm.download', [
            'year' => 2025,
        ]));

        $this->assertTrue(
            $response->isRedirect() || $response->getStatusCode() === 422,
            'Expected redirect/422, got: ' . $response->getStatusCode()
        );
    }

    // F84 - Download gagal jika year tidak dikirim → redirect/422
    public function test_download_rekap_skm_fails_without_year()
    {
        $response = $this->actingAs($this->superadmin)->get(route('superadmin.skm.download', [
            'month' => 1,
        ]));

        $this->assertTrue(
            $response->isRedirect() || $response->getStatusCode() === 422,
            'Expected redirect/422, got: ' . $response->getStatusCode()
        );
    }

    // F85 - Admin tidak bisa download rekap SKM (403)
    public function test_admin_cannot_download_rekap_skm()
    {
        $response = $this->actingAs($this->admin)->get(route('superadmin.skm.download', [
            'month' => 1,
            'year' => 2025,
        ]));

        $response->assertStatus(403);
    }

    // =========================================================================
    // SkmController@index (rekap per bulan)
    // =========================================================================

    // F86 - Rekap SKM tanpa filter menampilkan semua data (≥2 pasien)
    public function test_skm_rekap_without_filter_shows_all_data()
    {
        $response = $this->actingAs($this->superadmin)->get(route('superadmin.skm.rekap', [
            'month' => 1,
            'year' => 2025,
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('dataRekap');
        $dataRekap = $response->viewData('dataRekap');
        $this->assertGreaterThanOrEqual(2, count($dataRekap));
    }

    // F87 - Filter ?ruangan=R01 → dataRekap count = 1
    public function test_skm_rekap_filtered_by_ruangan_r01()
    {
        $response = $this->actingAs($this->superadmin)->get(route('superadmin.skm.rekap', [
            'month' => 1,
            'year' => 2025,
            'ruangan' => 'R01',
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('selectedRuangan', 'R01');
        $dataRekap = $response->viewData('dataRekap');
        $this->assertCount(1, $dataRekap);
    }

    // F88 - Filter ?ruangan=R02 → dataRekap count = 1
    public function test_skm_rekap_filtered_by_ruangan_r02()
    {
        $response = $this->actingAs($this->superadmin)->get(route('superadmin.skm.rekap', [
            'month' => 1,
            'year' => 2025,
            'ruangan' => 'R02',
        ]));

        $response->assertStatus(200);
        $dataRekap = $response->viewData('dataRekap');
        $this->assertCount(1, $dataRekap);
    }
}