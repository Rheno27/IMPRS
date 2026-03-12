<?php

namespace Tests\Feature;

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

/**
 * SkmHasilTest
 *
 * Mengcover use case Superadmin pada Use Case Diagram:
 *   - "Melihat Hasil SKM"
 *     → Route: superadmin.skm.hasil (SkmController@hasil)
 *   - "Download Hasil SKM"
 *     → Route: superadmin.skm.download (SkmController@downloadRekap)
 *   - "Manajemen Form SKM" — halaman GET edit pertanyaan
 *     → Route: superadmin.skm.edit2 (SkmController@editPertanyaan)
 *   - Filter rekap SKM per ruangan
 *     → Route: superadmin.skm.rekap dengan ?ruangan=
 *
 * Test IDs: F71–F82
 */
class SkmHasilTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superadmin;
    protected User $admin;
    protected Pertanyaan $pertanyaan;
    protected PilihanJawaban $pilihan;

    protected function setUp(): void
    {
        parent::setUp();

        Ruangan::firstOrCreate(['id_ruangan' => 'SP00'], ['nama_ruangan' => 'Super Admin']);
        Ruangan::firstOrCreate(['id_ruangan' => 'R01'], ['nama_ruangan' => 'Ruangan ICU']);
        Ruangan::firstOrCreate(['id_ruangan' => 'R02'], ['nama_ruangan' => 'Ruangan Nifas']);

        Kategori::firstOrCreate(['id_kategori' => 1], ['kategori' => 'Indikator Nasional Mutu']);

        // Buat struktur pertanyaan + pilihan jawaban agar SkmController tidak error
        $this->pertanyaan = Pertanyaan::firstOrCreate(
            ['id_pertanyaan' => 1],
            ['pertanyaan' => 'Bagaimana pelayanan petugas?', 'urutan' => 1]
        );

        $this->pilihan = PilihanJawaban::firstOrCreate(
            ['id_pilihan' => 1],
            ['id_pertanyaan' => 1, 'pilihan' => 'Sangat Baik', 'nilai' => 4]
        );

        PilihanJawaban::firstOrCreate(
            ['id_pilihan' => 2],
            ['id_pertanyaan' => 1, 'pilihan' => 'Baik', 'nilai' => 3]
        );

        // Pertanyaan kritik/saran (id = 16, tanpa pilihan jawaban)
        Pertanyaan::firstOrCreate(
            ['id_pertanyaan' => 16],
            ['pertanyaan' => 'Kritik dan Saran', 'urutan' => 16]
        );

        // Buat data responden di R01 untuk bulan Januari 2025
        $pasienR01 = BioPasien::create([
            'id_ruangan'    => 'R01',
            'no_rm'         => '11111',
            'umur'          => 30,
            'jenis_kelamin' => 'L',
            'pendidikan'    => 'SMA',
            'pekerjaan'     => 'Swasta',
        ]);

        Jawaban::create([
            'tanggal'      => '2025-01-10',
            'id_pasien'    => $pasienR01->id_pasien,
            'id_pertanyaan' => 1,
            'id_pilihan'   => 1,
            'hasil_nilai'  => 4,
        ]);

        // Buat data responden di R02 untuk bulan Januari 2025
        $pasienR02 = BioPasien::create([
            'id_ruangan'    => 'R02',
            'no_rm'         => '22222',
            'umur'          => 25,
            'jenis_kelamin' => 'P',
            'pendidikan'    => 'D3',
            'pekerjaan'     => 'PNS',
        ]);

        Jawaban::create([
            'tanggal'      => '2025-01-12',
            'id_pasien'    => $pasienR02->id_pasien,
            'id_pertanyaan' => 1,
            'id_pilihan'   => 2,
            'hasil_nilai'  => 3,
        ]);

        $this->superadmin = User::create([
            'id_user'      => 'SP001',
            'id_ruangan'   => 'SP00',
            'username'     => 'superadmin_skm',
            'password'     => Hash::make('password'),
            'nama_ruangan' => 'Super Admin',
        ]);

        $this->admin = User::create([
            'id_user'      => 'U001',
            'id_ruangan'   => 'R01',
            'username'     => 'admin_skm',
            'password'     => Hash::make('password'),
            'nama_ruangan' => 'Ruangan ICU',
        ]);
    }

    // =========================================================================
    // KELOMPOK A: MELIHAT HASIL SKM
    // Route: GET /superadmin/skm/hasil
    // Use Case Diagram: "Melihat Hasil SKM" (Superadmin)
    // =========================================================================

    // F71 - Superadmin dapat membuka halaman hasil SKM
    public function test_superadmin_can_view_hasil_skm()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.skm.hasil'));

        $response->assertStatus(200);
    }

    // F72 - Halaman hasil SKM mengirim semua variabel yang dibutuhkan view
    public function test_hasil_skm_passes_required_variables_to_view()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.skm.hasil'));

        $response->assertStatus(200);
        $response->assertViewHas('totalResponden');
        $response->assertViewHas('allSurveyCharts');
        $response->assertViewHas('listRuangan');
        $response->assertViewHas('selectedYear');
        $response->assertViewHas('selectedMonth');
        $response->assertViewHas('ruanganChart');
        $response->assertViewHas('jenisKelaminChart');
    }

    // F73 - Halaman hasil SKM menghitung totalResponden dengan benar
    public function test_hasil_skm_counts_total_responden_correctly()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.skm.hasil', [
                'month' => 1,
                'year'  => 2025,
            ]));

        $response->assertStatus(200);

        // setUp membuat 2 pasien dengan jawaban di Januari 2025
        $totalResponden = $response->viewData('totalResponden');
        $this->assertGreaterThanOrEqual(2, $totalResponden);
    }

    // F74 - Halaman hasil SKM menerima filter ?year= dan ?month=
    public function test_hasil_skm_accepts_year_month_filter()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.skm.hasil', [
                'year'  => 2025,
                'month' => 1,
            ]));

        $response->assertStatus(200);
        $response->assertViewHas('selectedYear', '2025');
        $response->assertViewHas('selectedMonth', '1');
    }

    // F75 - Filter ?ruangan= pada halaman hasil SKM menampilkan data ruangan tersebut
    public function test_hasil_skm_can_filter_by_ruangan()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.skm.hasil', [
                'year'    => 2025,
                'month'   => 1,
                'ruangan' => 'R01',
            ]));

        $response->assertStatus(200);

        // Hanya R01 yang difilter → totalResponden = 1
        $totalResponden = $response->viewData('totalResponden');
        $this->assertEquals(1, $totalResponden);
    }

    // F76 - Admin tidak dapat mengakses halaman hasil SKM
    public function test_admin_cannot_access_hasil_skm()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('superadmin.skm.hasil'));

        $response->assertStatus(403);
    }

    // F77 - Guest di-redirect ke halaman login saat akses hasil SKM
    public function test_guest_cannot_access_hasil_skm()
    {
        $response = $this->get(route('superadmin.skm.hasil'));

        $response->assertRedirect(route('login'));
    }

    // =========================================================================
    // KELOMPOK B: HALAMAN EDIT PERTANYAAN SKM
    // Route: GET /superadmin/skm/edit2
    // Use Case Diagram: "Manajemen Form SKM" (Superadmin)
    // =========================================================================

    // F78 - Superadmin dapat membuka halaman edit struktur pertanyaan SKM
    public function test_superadmin_can_view_edit_pertanyaan_skm()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.skm.edit2'));

        $response->assertStatus(200);
    }

    // F79 - Halaman edit pertanyaan mengirim surveyData ke view
    public function test_edit_pertanyaan_passes_survey_data_to_view()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.skm.edit2'));

        $response->assertStatus(200);
        $response->assertViewHas('surveyData');

        // surveyData harus berisi koleksi pertanyaan
        $surveyData = $response->viewData('surveyData');
        $this->assertGreaterThan(0, $surveyData->count());
    }

    // F80 - Admin tidak dapat membuka halaman edit pertanyaan SKM
    public function test_admin_cannot_access_edit_pertanyaan_skm()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('superadmin.skm.edit2'));

        $response->assertStatus(403);
    }

    // =========================================================================
    // KELOMPOK C: DOWNLOAD REKAP SKM
    // Route: GET /superadmin/skm/download
    // Use Case Diagram: "Download Hasil SKM" (Superadmin)
    // =========================================================================

    // F81 - Superadmin dapat download rekap SKM dalam format Excel
    public function test_superadmin_can_download_rekap_skm()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.skm.download', [
                'month' => 1,
                'year'  => 2025,
            ]));

        $response->assertStatus(200);

        // Verifikasi response berupa file Excel (pola sama dengan DashboardTest F47)
        $contentType = $response->headers->get('Content-Type');
        $this->assertTrue(
            str_contains($contentType, 'spreadsheet') ||
            str_contains($contentType, 'excel') ||
            str_contains($contentType, 'octet-stream'),
            "Expected Excel Content-Type, got: {$contentType}"
        );
    }

    // F82 - Download rekap SKM dapat difilter per ruangan (opsional)
    public function test_superadmin_can_download_rekap_skm_filtered_by_ruangan()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.skm.download', [
                'month'   => 1,
                'year'    => 2025,
                'ruangan' => 'R01',
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

    // F83 - Download rekap SKM gagal jika parameter month tidak dikirim
    public function test_download_rekap_skm_fails_without_month()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.skm.download', [
                // month sengaja tidak dikirim
                'year' => 2025,
            ]));

        // SkmController: $request->validate(['month' => 'required|numeric', ...])
        $this->assertTrue(
            $response->isRedirect() || $response->getStatusCode() === 422,
            'Expected redirect/422, got: ' . $response->getStatusCode()
        );
    }

    // F84 - Download rekap SKM gagal jika parameter year tidak dikirim
    public function test_download_rekap_skm_fails_without_year()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.skm.download', [
                'month' => 1,
                // year sengaja tidak dikirim
            ]));

        $this->assertTrue(
            $response->isRedirect() || $response->getStatusCode() === 422,
            'Expected redirect/422, got: ' . $response->getStatusCode()
        );
    }

    // F85 - Admin tidak dapat download rekap SKM
    public function test_admin_cannot_download_rekap_skm()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('superadmin.skm.download', [
                'month' => 1,
                'year'  => 2025,
            ]));

        $response->assertStatus(403);
    }

    // =========================================================================
    // KELOMPOK D: FILTER REKAP SKM PER RUANGAN
    // Route: GET /superadmin/skm/rekap?ruangan=
    // Use Case Diagram: "Melihat Hasil SKM" — filter
    // =========================================================================

    // F86 - Rekap SKM tanpa filter menampilkan semua data
    public function test_skm_rekap_without_filter_shows_all_data()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.skm.rekap', [
                'month' => 1,
                'year'  => 2025,
            ]));

        $response->assertStatus(200);
        $response->assertViewHas('dataRekap');

        // Tanpa filter: dataRekap harus berisi R01 + R02 = minimal 2 pasien
        $dataRekap = $response->viewData('dataRekap');
        $this->assertGreaterThanOrEqual(2, count($dataRekap));
    }

    // F87 - Filter ?ruangan=R01 hanya menampilkan data milik R01
    public function test_skm_rekap_filtered_by_ruangan_returns_only_that_ruangan_data()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.skm.rekap', [
                'month'   => 1,
                'year'    => 2025,
                'ruangan' => 'R01',
            ]));

        $response->assertStatus(200);
        $response->assertViewHas('dataRekap');
        $response->assertViewHas('selectedRuangan', 'R01');

        // Dengan filter R01: hanya 1 pasien (no_rm=11111)
        $dataRekap = $response->viewData('dataRekap');
        $this->assertCount(1, $dataRekap);
    }

    // F88 - Filter ?ruangan=R02 hanya menampilkan data milik R02
    public function test_skm_rekap_filtered_by_ruangan_r02()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.skm.rekap', [
                'month'   => 1,
                'year'    => 2025,
                'ruangan' => 'R02',
            ]));

        $response->assertStatus(200);

        // Dengan filter R02: hanya 1 pasien (no_rm=22222)
        $dataRekap = $response->viewData('dataRekap');
        $this->assertCount(1, $dataRekap);
    }
}
