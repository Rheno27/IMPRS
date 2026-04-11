<?php

namespace Tests\Feature\Superadmin;

use App\Models\IndikatorMutu;
use App\Models\IndikatorRuangan;
use App\Models\Kategori;
use App\Models\MutuRuangan;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SDashboardControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superadmin;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Ruangan::firstOrCreate(['id_ruangan' => 'SP00'], ['nama_ruangan' => 'Super Admin']);
        Ruangan::firstOrCreate(['id_ruangan' => 'R01'], ['nama_ruangan' => 'Ruangan ICU']);

        Kategori::firstOrCreate(['id_kategori' => 1], ['kategori' => 'Indikator Nasional Mutu']);
        Kategori::firstOrCreate(['id_kategori' => 2], ['kategori' => 'Indikator Mutu Prioritas Rumah Sakit']);
        Kategori::firstOrCreate(['id_kategori' => 3], ['kategori' => 'Indikator Mutu Prioritas Unit']);

        $indikator = IndikatorMutu::create([
            'id_kategori' => 1,
            'variabel' => 'Kepatuhan Penggunaan APD',
            'standar' => '100',
        ]);

        $ir = IndikatorRuangan::create([
            'id_ruangan' => 'R01',
            'id_indikator' => $indikator->id_indikator,
            'active' => true,
        ]);

        MutuRuangan::create([
            'id_indikator_ruangan' => $ir->id_indikator_ruangan,
            'tanggal' => '2025-03-15',
            'pasien_sesuai' => 8,
            'total_pasien' => 10,
        ]);

        $this->superadmin = User::create([
            'id_user' => 'SP001',
            'id_ruangan' => 'SP00',
            'username' => 'superadmin_sdash',
            'password' => Hash::make('password'),
            'nama_ruangan' => 'Super Admin',
        ]);

        $this->admin = User::create([
            'id_user' => 'U001',
            'id_ruangan' => 'R01',
            'username' => 'admin_sdash',
            'password' => Hash::make('password'),
            'nama_ruangan' => 'Ruangan ICU',
        ]);
    }

    // =========================================================================
    // index()
    // =========================================================================

    // F-GAP-4 - Superadmin akses dashboard tampil 200 dengan kategori default INM
    public function test_superadmin_can_access_sdashboard_with_default_kategori()
    {
        $response = $this->actingAs($this->superadmin)->get(route('superadmin.dashboard'));
        $response->assertStatus(200);
    }

    // F-GAP-5 - Dashboard menerima filter ?kategori= dan ?tahun=
    public function test_sdashboard_accepts_kategori_and_tahun_filter()
    {
        $response = $this->actingAs($this->superadmin)->get(route('superadmin.dashboard', [
            'kategori' => 'Indikator Nasional Mutu',
            'tahun' => 2025,
        ]));

        $response->assertStatus(200);
    }

    // F-GAP-6 - View memiliki variabel indikatorData, selectedKategori, tahun
    public function test_sdashboard_passes_required_variables_to_view()
    {
        $response = $this->actingAs($this->superadmin)->get(route('superadmin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('indikatorData');
        $response->assertViewHas('selectedKategori');
        $response->assertViewHas('tahun');
    }

    // F-GAP-7 - Admin tidak bisa akses SDashboard (403)
    public function test_admin_cannot_access_sdashboard()
    {
        $response = $this->actingAs($this->admin)->get(route('superadmin.dashboard'));
        $response->assertStatus(403);
    }

    // F-GAP-8 - Guest redirect login dari SDashboard
    public function test_guest_cannot_access_sdashboard()
    {
        $response = $this->get(route('superadmin.dashboard'));
        $response->assertRedirect(route('login'));
    }

    // =========================================================================
    // downloadRekapIndikator()
    // =========================================================================

    // F47 - Superadmin download rekap per ruangan → Excel 200
    public function test_superadmin_can_download_rekap_per_ruangan()
    {
        $response = $this->actingAs($this->superadmin)->get(route('superadmin.download_rekap', [
            'ruangan_id' => 'R01',
            'bulan' => 1,
            'tahun' => 2025,
        ]));

        $response->assertStatus(200);
        $contentType = $response->headers->get('Content-Type');
        $this->assertTrue(
            str_contains($contentType, 'spreadsheet') ||
            str_contains($contentType, 'excel') ||
            str_contains($contentType, 'octet-stream')
        );
    }

    // F64 - Download rekap Indikator Nasional Mutu → Excel 200
    public function test_superadmin_can_download_rekap_indikator_nasional_mutu()
    {
        $response = $this->actingAs($this->superadmin)->get(route('superadmin.download_rekap_indikator', [
            'tahun' => 2025,
            'kategori' => 'Indikator Nasional Mutu',
        ]));

        $response->assertStatus(200);
        $contentType = $response->headers->get('Content-Type');
        $this->assertTrue(
            str_contains($contentType, 'spreadsheet') ||
            str_contains($contentType, 'excel') ||
            str_contains($contentType, 'octet-stream')
        );
    }

    // F65 - Download rekap Indikator Mutu Prioritas Rumah Sakit → Excel 200
    public function test_superadmin_can_download_rekap_indikator_prioritas_rs()
    {
        $response = $this->actingAs($this->superadmin)->get(route('superadmin.download_rekap_indikator', [
            'tahun' => 2025,
            'kategori' => 'Indikator Mutu Prioritas Rumah Sakit',
        ]));

        $response->assertStatus(200);
        $contentType = $response->headers->get('Content-Type');
        $this->assertTrue(
            str_contains($contentType, 'spreadsheet') ||
            str_contains($contentType, 'excel') ||
            str_contains($contentType, 'octet-stream')
        );
    }

    // F66 - Download rekap Indikator Mutu Prioritas Unit → Excel 200
    public function test_superadmin_can_download_rekap_indikator_prioritas_unit()
    {
        $response = $this->actingAs($this->superadmin)->get(route('superadmin.download_rekap_indikator', [
            'tahun' => 2025,
            'kategori' => 'Indikator Mutu Prioritas Unit',
        ]));

        $response->assertStatus(200);
        $contentType = $response->headers->get('Content-Type');
        $this->assertTrue(
            str_contains($contentType, 'spreadsheet') ||
            str_contains($contentType, 'excel') ||
            str_contains($contentType, 'octet-stream')
        );
    }

    // F67 - Download rekap gagal jika tahun tidak dikirim → redirect/422
    public function test_download_rekap_indikator_fails_without_tahun()
    {
        $response = $this->actingAs($this->superadmin)->get(route('superadmin.download_rekap_indikator', [
            'kategori' => 'Indikator Nasional Mutu',
        ]));

        $this->assertTrue(
            $response->isRedirect() || $response->getStatusCode() === 422,
            'Expected redirect/422, got: ' . $response->getStatusCode()
        );
    }

    // F68 - Download rekap gagal jika kategori tidak dikirim → redirect/422
    public function test_download_rekap_indikator_fails_without_kategori()
    {
        $response = $this->actingAs($this->superadmin)->get(route('superadmin.download_rekap_indikator', [
            'tahun' => 2025,
        ]));

        $this->assertTrue(
            $response->isRedirect() || $response->getStatusCode() === 422,
            'Expected redirect/422, got: ' . $response->getStatusCode()
        );
    }

    // F69 - Admin tidak bisa download rekap indikator (403)
    public function test_admin_cannot_download_rekap_indikator()
    {
        $response = $this->actingAs($this->admin)->get(route('superadmin.download_rekap_indikator', [
            'tahun' => 2025,
            'kategori' => 'Indikator Nasional Mutu',
        ]));

        $response->assertStatus(403);
    }

    // F70 - Guest tidak bisa download rekap indikator → redirect login
    public function test_guest_cannot_download_rekap_indikator()
    {
        $response = $this->get(route('superadmin.download_rekap_indikator', [
            'tahun' => 2025,
            'kategori' => 'Indikator Nasional Mutu',
        ]));

        $response->assertRedirect(route('login'));
    }
}