<?php

namespace Tests\Feature\Admin;

use App\Models\IndikatorMutu;
use App\Models\IndikatorRuangan;
use App\Models\Kategori;
use App\Models\MutuRuangan;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected User $superadmin;

    protected function setUp(): void
    {
        parent::setUp();

        Ruangan::firstOrCreate(['id_ruangan' => 'SP00'], ['nama_ruangan' => 'Superadmin']);
        Ruangan::firstOrCreate(['id_ruangan' => 'R01'], ['nama_ruangan' => 'Ruangan A']);

        Kategori::firstOrCreate(['id_kategori' => 1], ['kategori' => 'Indikator Nasional Mutu']);

        $indikator = IndikatorMutu::create([
            'id_kategori' => 1,
            'variabel' => 'Indikator Dashboard Test',
            'standar' => '90',
        ]);

        $ir = IndikatorRuangan::create([
            'id_ruangan' => 'R01',
            'id_indikator' => $indikator->id_indikator,
            'active' => true,
        ]);

        MutuRuangan::create([
            'id_indikator_ruangan' => $ir->id_indikator_ruangan,
            'tanggal' => now()->format('Y-m-15'),
            'pasien_sesuai' => 8,
            'total_pasien' => 10,
        ]);

        $this->admin = User::create([
            'id_user' => 'U001',
            'id_ruangan' => 'R01',
            'username' => 'admin_dash',
            'password' => Hash::make('password'),
            'nama_ruangan' => 'Ruangan A',
        ]);

        $this->superadmin = User::create([
            'id_user' => 'SP001',
            'id_ruangan' => 'SP00',
            'username' => 'superadmin_dash',
            'password' => Hash::make('password'),
            'nama_ruangan' => 'Super Admin',
        ]);
    }

    // =========================================================================
    // index()
    // =========================================================================

    // F44 - Admin dashboard menampilkan data bulan berjalan (200)
    public function test_admin_dashboard_shows_current_month_data()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
    }

    // F45 - Admin dashboard menerima query ?bulan= dan ?tahun= custom
    public function test_admin_dashboard_accepts_custom_bulan_tahun()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard', [
            'bulan' => 1,
            'tahun' => 2025,
        ]));

        $response->assertStatus(200);
    }

    // F46 - Dashboard redirect jika bulan tidak valid (> 12)
    public function test_dashboard_redirects_when_bulan_invalid()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard', [
            'bulan' => 13,
            'tahun' => 2025,
        ]));

        $this->assertTrue(
            $response->isRedirect() || $response->getStatusCode() === 422
        );
    }

    // =========================================================================
    // downloadRekap()
    // =========================================================================

    // F-GAP-1 - Admin download rekap milik sendiri → response Excel 200
    public function test_admin_can_download_own_rekap()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.download_rekap', [
            'bulan' => 1,
            'tahun' => 2025,
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

    // F-GAP-2 - Download rekap gagal jika bulan tidak dikirim → redirect/422
    public function test_download_rekap_fails_without_bulan()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.download_rekap', [
            'tahun' => 2025,
        ]));

        $this->assertTrue(
            $response->isRedirect() || $response->getStatusCode() === 422,
            'Expected redirect/422, got: ' . $response->getStatusCode()
        );
    }

    // F-GAP-3 - Guest tidak bisa download rekap admin → redirect login
    public function test_guest_cannot_download_rekap_admin()
    {
        $response = $this->get(route('admin.download_rekap', [
            'bulan' => 1,
            'tahun' => 2025,
        ]));

        $response->assertRedirect(route('login'));
    }
}