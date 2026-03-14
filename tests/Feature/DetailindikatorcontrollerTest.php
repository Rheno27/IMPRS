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

class DetailIndikatorControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superadmin;
    protected User $admin;
    protected Ruangan $ruangan;
    protected IndikatorRuangan $ir;

    protected function setUp(): void
    {
        parent::setUp();

        Ruangan::firstOrCreate(['id_ruangan' => 'SP00'], ['nama_ruangan' => 'Super Admin']);
        $this->ruangan = Ruangan::firstOrCreate(
            ['id_ruangan' => 'R01'],
            ['nama_ruangan' => 'Ruangan ICU']
        );

        Kategori::firstOrCreate(['id_kategori' => 1], ['kategori' => 'Indikator Nasional Mutu']);

        $indikator = IndikatorMutu::create([
            'id_kategori' => 1,
            'variabel' => 'Kepatuhan Kebersihan Tangan',
            'standar' => '85',
        ]);

        $this->ir = IndikatorRuangan::create([
            'id_ruangan' => 'R01',
            'id_indikator' => $indikator->id_indikator,
            'active' => true,
        ]);

        MutuRuangan::create([
            'id_indikator_ruangan' => $this->ir->id_indikator_ruangan,
            'tanggal' => now()->format('Y-m-15'),
            'pasien_sesuai' => 9,
            'total_pasien' => 10,
        ]);

        $this->superadmin = User::create([
            'id_user' => 'SP001',
            'id_ruangan' => 'SP00',
            'username' => 'superadmin_detail',
            'password' => Hash::make('password'),
            'nama_ruangan' => 'Super Admin',
        ]);

        $this->admin = User::create([
            'id_user' => 'U001',
            'id_ruangan' => 'R01',
            'username' => 'admin_detail',
            'password' => Hash::make('password'),
            'nama_ruangan' => 'Ruangan ICU',
        ]);
    }

    // =========================================================================
    // show()
    // =========================================================================

    // F53 - Superadmin membuka halaman detail indikator per ruangan (200)
    public function test_superadmin_can_view_detail_indikator_per_ruangan()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.ruangan.detail', ['ruangan' => $this->ruangan->id_ruangan]));

        $response->assertStatus(200);
    }

    // F54 - View memiliki semua variabel yang dibutuhkan
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

    // F55 - Menerima filter ?bulan= dan ?tahun= dari query string
    public function test_detail_page_accepts_bulan_tahun_filter()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.ruangan.detail', [
                'ruangan' => $this->ruangan->id_ruangan,
                'bulan' => 3,
                'tahun' => 2025,
            ]));

        $response->assertStatus(200);
        $response->assertViewHas('bulan', 3);
        $response->assertViewHas('tahun', 2025);
    }

    // F56 - Menerima filter ?kategori= dan meneruskannya ke view
    public function test_detail_page_passes_selected_kategori_to_view()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.ruangan.detail', [
                'ruangan' => $this->ruangan->id_ruangan,
                'kategori' => 'Indikator Nasional Mutu',
            ]));

        $response->assertStatus(200);
        $response->assertViewHas('selectedKategori', 'Indikator Nasional Mutu');
    }

    // F57 - indikatorData selalu memiliki entri 'Kepuasan Masyarakat' di akhir
    public function test_detail_page_indikator_data_includes_kepuasan_masyarakat()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.ruangan.detail', ['ruangan' => $this->ruangan->id_ruangan]));

        $response->assertStatus(200);
        $indikatorData = $response->viewData('indikatorData');
        $this->assertIsArray($indikatorData);

        $last = end($indikatorData);
        $this->assertEquals('Kepuasan Masyarakat', $last['variabel']);
    }

    // F58 - Admin tidak bisa akses detail ruangan (403)
    public function test_admin_cannot_access_detail_ruangan()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('superadmin.ruangan.detail', ['ruangan' => $this->ruangan->id_ruangan]));

        $response->assertStatus(403);
    }

    // F59 - Guest redirect login dari detail ruangan
    public function test_guest_cannot_access_detail_ruangan()
    {
        $response = $this->get(
            route('superadmin.ruangan.detail', ['ruangan' => $this->ruangan->id_ruangan])
        );

        $response->assertRedirect(route('login'));
    }

    // =========================================================================
    // downloadRekap()
    // =========================================================================

    // F60 - Superadmin download rekap per ruangan → Excel 200
    public function test_superadmin_can_download_rekap_per_ruangan()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.download_rekap', [
                'bulan' => now()->month,
                'tahun' => now()->year,
                'ruangan_id' => 'R01',
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

    // F61 - Gagal jika bulan tidak dikirim → redirect/422
    public function test_download_rekap_fails_without_bulan()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.download_rekap', [
                'tahun' => 2025,
                'ruangan_id' => 'R01',
            ]));

        $this->assertTrue(
            $response->isRedirect() || $response->getStatusCode() === 422,
            'Expected redirect/422, got: ' . $response->getStatusCode()
        );
    }

    // F62 - Gagal jika ruangan_id tidak dikirim → redirect/422
    public function test_download_rekap_fails_without_ruangan_id()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.download_rekap', [
                'bulan' => 1,
                'tahun' => 2025,
            ]));

        $this->assertTrue(
            $response->isRedirect() || $response->getStatusCode() === 422,
            'Expected redirect/422, got: ' . $response->getStatusCode()
        );
    }

    // F63 - Admin tidak bisa pakai route download rekap superadmin (403)
    public function test_admin_cannot_use_superadmin_download_rekap()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('superadmin.download_rekap', [
                'bulan' => 1,
                'tahun' => 2025,
                'ruangan_id' => 'R01',
            ]));

        $response->assertStatus(403);
    }
}