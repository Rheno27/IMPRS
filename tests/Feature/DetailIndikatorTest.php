<?php

namespace Tests\Feature;

use App\Models\IndikatorMutu;
use App\Models\IndikatorRuangan;
use App\Models\Kategori;
use App\Models\MutuRuangan;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * DetailIndikatorTest
 *
 * Mengcover use case Superadmin pada Use Case Diagram:
 *   - "Melihat Pelaporan Mutu" per ruangan
 *     → Route: superadmin.ruangan.detail (DetailIndikatorController@show)
 *   - "Download Pelaporan Mutu" per ruangan
 *     → Route: superadmin.download_rekap (DetailIndikatorController@downloadRekap)
 *
 * Test IDs: F53–F62 (lanjutan dari 52 test yang sudah ada)
 */
class DetailIndikatorTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superadmin;
    protected User $admin;
    protected Ruangan $ruangan;
    protected IndikatorMutu $indikator;
    protected IndikatorRuangan $indikatorRuangan;

    protected function setUp(): void
    {
        parent::setUp();

        // Pastikan ruangan FK tersedia — pakai firstOrCreate sama seperti test lain
        Ruangan::firstOrCreate(['id_ruangan' => 'SP00'], ['nama_ruangan' => 'Super Admin']);
        $this->ruangan = Ruangan::firstOrCreate(['id_ruangan' => 'R01'], ['nama_ruangan' => 'Ruangan ICU']);

        // Kategori sebagai FK indikator_mutu
        Kategori::firstOrCreate(['id_kategori' => 1], ['kategori' => 'Indikator Nasional Mutu']);

        // Buat indikator dan assign ke ruangan
        $this->indikator = IndikatorMutu::create([
            'id_kategori' => 1,
            'variabel'    => 'Kepatuhan Kebersihan Tangan',
            'standar'     => '85',
        ]);

        $this->indikatorRuangan = IndikatorRuangan::create([
            'id_ruangan'   => 'R01',
            'id_indikator' => $this->indikator->id_indikator,
            'active'       => true,
        ]);

        // Data mutu agar controller tidak kosong
        MutuRuangan::create([
            'id_indikator_ruangan' => $this->indikatorRuangan->id_indikator_ruangan,
            'tanggal'              => now()->format('Y-m-15'),
            'pasien_sesuai'        => 9,
            'total_pasien'         => 10,
        ]);

        $this->superadmin = User::create([
            'id_user'      => 'SP001',
            'id_ruangan'   => 'SP00',
            'username'     => 'superadmin_detail',
            'password'     => Hash::make('password'),
            'nama_ruangan' => 'Super Admin',
        ]);

        $this->admin = User::create([
            'id_user'      => 'U001',
            'id_ruangan'   => 'R01',
            'username'     => 'admin_detail',
            'password'     => Hash::make('password'),
            'nama_ruangan' => 'Ruangan ICU',
        ]);
    }

    // =========================================================================
    // KELOMPOK A: VIEW DETAIL RUANGAN
    // Route: GET /superadmin/ruangan/{ruangan}/detail
    // Use Case Diagram: "Melihat Pelaporan Mutu" (Superadmin)
    // =========================================================================

    // F53 - Superadmin dapat membuka halaman detail indikator per ruangan
    public function test_superadmin_can_view_detail_indikator_per_ruangan()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.ruangan.detail', ['ruangan' => $this->ruangan->id_ruangan]));

        $response->assertStatus(200);
    }

    // F54 - Halaman detail mengirim semua variabel yang dibutuhkan view
    public function test_detail_page_passes_required_variables_to_view()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.ruangan.detail', ['ruangan' => $this->ruangan->id_ruangan]));

        $response->assertStatus(200);
        $response->assertViewHas('ruangan');
        $response->assertViewHas('indikatorData');
        $response->assertViewHas('jumlahHari');
        $response->assertViewHas('namaBulan');
        $response->assertViewHas('chartSeries');
        $response->assertViewHas('bulan');
        $response->assertViewHas('tahun');
    }

    // F55 - Halaman detail menerima filter ?bulan= dan ?tahun= dari query string
    public function test_detail_page_accepts_bulan_tahun_filter()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.ruangan.detail', [
                'ruangan' => $this->ruangan->id_ruangan,
                'bulan'   => 3,
                'tahun'   => 2025,
            ]));

        $response->assertStatus(200);
        $response->assertViewHas('bulan', 3);
        $response->assertViewHas('tahun', 2025);
    }

    // F56 - Halaman detail menerima filter ?kategori= dan meneruskannya ke view
    public function test_detail_page_passes_selected_kategori_to_view()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.ruangan.detail', [
                'ruangan'  => $this->ruangan->id_ruangan,
                'kategori' => 'Indikator Nasional Mutu',
            ]));

        $response->assertStatus(200);
        $response->assertViewHas('selectedKategori', 'Indikator Nasional Mutu');
    }

    // F57 - indikatorData dari view berisi entri Kepuasan Masyarakat (dari getSkmData)
    public function test_detail_page_indikator_data_includes_kepuasan_masyarakat()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.ruangan.detail', ['ruangan' => $this->ruangan->id_ruangan]));

        $response->assertStatus(200);

        $indikatorData = $response->viewData('indikatorData');
        $this->assertIsArray($indikatorData);

        // Entri terakhir selalu Kepuasan Masyarakat (ditambahkan controller dari getSkmData)
        $last = end($indikatorData);
        $this->assertEquals('Kepuasan Masyarakat', $last['variabel']);
    }

    // F58 - Admin (bukan superadmin) tidak dapat mengakses halaman detail ruangan
    public function test_admin_cannot_access_detail_ruangan()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('superadmin.ruangan.detail', ['ruangan' => $this->ruangan->id_ruangan]));

        $response->assertStatus(403);
    }

    // F59 - Guest (belum login) di-redirect ke halaman login
    public function test_guest_cannot_access_detail_ruangan()
    {
        $response = $this->get(
            route('superadmin.ruangan.detail', ['ruangan' => $this->ruangan->id_ruangan])
        );

        $response->assertRedirect(route('login'));
    }

    // =========================================================================
    // KELOMPOK B: DOWNLOAD REKAP PER RUANGAN
    // Route: GET /superadmin/download-rekap?bulan=&tahun=&ruangan_id=
    // Use Case Diagram: "Download Pelaporan Mutu" (Superadmin)
    // =========================================================================

    // F60 - Superadmin dapat download rekap mutu per ruangan dalam format Excel
    public function test_superadmin_can_download_rekap_per_ruangan()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.download_rekap', [
                'bulan'      => now()->month,
                'tahun'      => now()->year,
                'ruangan_id' => 'R01',
            ]));

        $response->assertStatus(200);

        // Verifikasi response berupa file Excel (sesuai pola DashboardTest F47)
        $contentType = $response->headers->get('Content-Type');
        $this->assertTrue(
            str_contains($contentType, 'spreadsheet') ||
            str_contains($contentType, 'excel') ||
            str_contains($contentType, 'octet-stream'),
            "Expected Excel Content-Type, got: {$contentType}"
        );
    }

    // F61 - Download rekap gagal jika parameter bulan tidak dikirim
    public function test_download_rekap_fails_without_bulan()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.download_rekap', [
                // bulan sengaja tidak dikirim
                'tahun'      => 2025,
                'ruangan_id' => 'R01',
            ]));

        // Controller menggunakan $request->validate() → redirect dengan error
        $this->assertTrue(
            $response->isRedirect() || $response->getStatusCode() === 422,
            'Expected redirect/422, got: ' . $response->getStatusCode()
        );
    }

    // F62 - Download rekap gagal jika parameter ruangan_id tidak dikirim
    public function test_download_rekap_fails_without_ruangan_id()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.download_rekap', [
                'bulan' => 1,
                'tahun' => 2025,
                // ruangan_id sengaja tidak dikirim
            ]));

        $this->assertTrue(
            $response->isRedirect() || $response->getStatusCode() === 422,
            'Expected redirect/422, got: ' . $response->getStatusCode()
        );
    }

    // F63 - Admin tidak dapat menggunakan route download rekap superadmin
    public function test_admin_cannot_use_superadmin_download_rekap()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('superadmin.download_rekap', [
                'bulan'      => 1,
                'tahun'      => 2025,
                'ruangan_id' => 'R01',
            ]));

        $response->assertStatus(403);
    }
}
