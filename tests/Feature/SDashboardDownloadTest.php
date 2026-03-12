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
 * SDashboardDownloadTest
 *
 * Mengcover use case Superadmin pada Use Case Diagram:
 *   - "Download Pelaporan Mutu" per kategori
 *     → Route: superadmin.download_rekap_indikator
 *     → Controller: SDashboardController@downloadRekapIndikator
 *     → Export: RekapPerIndikatorExport
 *
 * Test IDs: F64–F68
 */
class SDashboardDownloadTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superadmin;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Ruangan::firstOrCreate(['id_ruangan' => 'SP00'], ['nama_ruangan' => 'Super Admin']);
        Ruangan::firstOrCreate(['id_ruangan' => 'R01'], ['nama_ruangan' => 'Ruangan ICU']);

        // Siapkan semua kategori yang dipakai di form download
        Kategori::firstOrCreate(['id_kategori' => 1], ['kategori' => 'Indikator Nasional Mutu']);
        Kategori::firstOrCreate(['id_kategori' => 2], ['kategori' => 'Indikator Mutu Prioritas Rumah Sakit']);
        Kategori::firstOrCreate(['id_kategori' => 3], ['kategori' => 'Indikator Mutu Prioritas Unit']);

        // Buat indikator dengan kategori INM agar export tidak kosong
        $indikator = IndikatorMutu::create([
            'id_kategori' => 1,
            'variabel'    => 'Kepatuhan Penggunaan APD',
            'standar'     => '100',
        ]);

        $indikatorRuangan = IndikatorRuangan::create([
            'id_ruangan'   => 'R01',
            'id_indikator' => $indikator->id_indikator,
            'active'       => true,
        ]);

        MutuRuangan::create([
            'id_indikator_ruangan' => $indikatorRuangan->id_indikator_ruangan,
            'tanggal'              => '2025-03-15',
            'pasien_sesuai'        => 8,
            'total_pasien'         => 10,
        ]);

        $this->superadmin = User::create([
            'id_user'      => 'SP001',
            'id_ruangan'   => 'SP00',
            'username'     => 'superadmin_sdash',
            'password'     => Hash::make('password'),
            'nama_ruangan' => 'Super Admin',
        ]);

        $this->admin = User::create([
            'id_user'      => 'U001',
            'id_ruangan'   => 'R01',
            'username'     => 'admin_sdash',
            'password'     => Hash::make('password'),
            'nama_ruangan' => 'Ruangan ICU',
        ]);
    }

    // =========================================================================
    // KELOMPOK A: DOWNLOAD REKAP PER KATEGORI
    // Route: GET /superadmin/download-rekap-indikator?tahun=&kategori=
    // Use Case Diagram: "Download Pelaporan Mutu" (Superadmin)
    // =========================================================================

    // F64 - Superadmin dapat download rekap Indikator Nasional Mutu (INM)
    public function test_superadmin_can_download_rekap_indikator_nasional_mutu()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.download_rekap_indikator', [
                'tahun'    => 2025,
                'kategori' => 'Indikator Nasional Mutu',
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

    // F65 - Superadmin dapat download rekap Indikator Mutu Prioritas Rumah Sakit
    public function test_superadmin_can_download_rekap_indikator_prioritas_rs()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.download_rekap_indikator', [
                'tahun'    => 2025,
                'kategori' => 'Indikator Mutu Prioritas Rumah Sakit',
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

    // F66 - Superadmin dapat download rekap Indikator Mutu Prioritas Unit (IMPU)
    public function test_superadmin_can_download_rekap_indikator_prioritas_unit()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.download_rekap_indikator', [
                'tahun'    => 2025,
                'kategori' => 'Indikator Mutu Prioritas Unit',
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

    // F67 - Download rekap gagal jika parameter tahun tidak dikirim
    public function test_download_rekap_indikator_fails_without_tahun()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.download_rekap_indikator', [
                // tahun sengaja tidak dikirim
                'kategori' => 'Indikator Nasional Mutu',
            ]));

        // Controller menggunakan $request->validate() → redirect dengan error
        $this->assertTrue(
            $response->isRedirect() || $response->getStatusCode() === 422,
            'Expected redirect/422, got: ' . $response->getStatusCode()
        );
    }

    // F68 - Download rekap gagal jika parameter kategori tidak dikirim
    public function test_download_rekap_indikator_fails_without_kategori()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.download_rekap_indikator', [
                'tahun' => 2025,
                // kategori sengaja tidak dikirim
            ]));

        $this->assertTrue(
            $response->isRedirect() || $response->getStatusCode() === 422,
            'Expected redirect/422, got: ' . $response->getStatusCode()
        );
    }

    // F69 - Admin tidak dapat menggunakan route download rekap indikator superadmin
    public function test_admin_cannot_download_rekap_indikator()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('superadmin.download_rekap_indikator', [
                'tahun'    => 2025,
                'kategori' => 'Indikator Nasional Mutu',
            ]));

        $response->assertStatus(403);
    }

    // F70 - Guest tidak dapat menggunakan route download rekap indikator
    public function test_guest_cannot_download_rekap_indikator()
    {
        $response = $this->get(route('superadmin.download_rekap_indikator', [
            'tahun'    => 2025,
            'kategori' => 'Indikator Nasional Mutu',
        ]));

        $response->assertRedirect(route('login'));
    }
}
