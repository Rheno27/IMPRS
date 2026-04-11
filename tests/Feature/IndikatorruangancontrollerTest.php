<?php

namespace Tests\Feature\Superadmin;

use App\Models\IndikatorMutu;
use App\Models\IndikatorRuangan;
use App\Models\Kategori;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class IndikatorRuanganControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superadmin;
    protected IndikatorMutu $im1;
    protected IndikatorMutu $im2;

    protected function setUp(): void
    {
        parent::setUp();

        Ruangan::firstOrCreate(['id_ruangan' => 'SP00'], ['nama_ruangan' => 'Superadmin']);
        Ruangan::firstOrCreate(['id_ruangan' => 'R01'], ['nama_ruangan' => 'Ruangan A']);
        Kategori::firstOrCreate(['id_kategori' => 1], ['kategori' => 'Indikator Nasional Mutu']);

        $this->im1 = IndikatorMutu::create([
            'id_kategori' => 1,
            'variabel' => 'Indikator A',
            'standar' => '90',
        ]);
        $this->im2 = IndikatorMutu::create([
            'id_kategori' => 1,
            'variabel' => 'Indikator B',
            'standar' => '85',
        ]);

        $this->superadmin = User::create([
            'id_user' => 'SP001',
            'id_ruangan' => 'SP00',
            'username' => 'superadmin_ir',
            'password' => Hash::make('password'),
            'nama_ruangan' => 'Super Admin',
        ]);
    }

    // =========================================================================
    // store() — assign indikator ke ruangan
    // =========================================================================

    // F32 - Superadmin assign indikator ke ruangan → assertDatabaseHas active=true
    public function test_superadmin_can_assign_indikator_to_ruangan()
    {
        $response = $this->actingAs($this->superadmin)
            ->post(route('superadmin.ruangan.add_indikator'), [
                'id_ruangan' => 'R01',
                'id_indikator_baru' => $this->im1->id_indikator,
            ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('indikator_ruangan', [
            'id_ruangan' => 'R01',
            'id_indikator' => $this->im1->id_indikator,
            'active' => true,
        ]);
    }

    // F33 - Tidak bisa assign indikator aktif yang sama dua kali
    // Controller pakai redirect()->back()->with('error') bukan withErrors()
    public function test_cannot_assign_duplicate_active_indikator_to_same_ruangan()
    {
        IndikatorRuangan::create([
            'id_ruangan' => 'R01',
            'id_indikator' => $this->im1->id_indikator,
            'active' => true,
        ]);

        $response = $this->actingAs($this->superadmin)
            ->post(route('superadmin.ruangan.add_indikator'), [
                'id_ruangan' => 'R01',
                'id_indikator_baru' => $this->im1->id_indikator,
            ]);

        // Controller: redirect()->back()->with('error', '...') bukan withErrors()
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    // =========================================================================
    // edit() — tampil halaman edit indikator ruangan
    // =========================================================================

    // F36 - Halaman edit hanya menampilkan indikator aktif
    public function test_edit_page_shows_only_active_indikators()
    {
        $activeIr = IndikatorRuangan::create([
            'id_ruangan' => 'R01',
            'id_indikator' => $this->im1->id_indikator,
            'active' => true,
        ]);
        $inactiveIr = IndikatorRuangan::create([
            'id_ruangan' => 'R01',
            'id_indikator' => $this->im2->id_indikator,
            'active' => false,
        ]);

        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.ruangan.edit_indikator', 'R01'));

        $response->assertStatus(200);
        $response->assertSee('editModal-' . $activeIr->id_indikator_ruangan);
        $response->assertDontSee('editModal-' . $inactiveIr->id_indikator_ruangan);
    }

    // =========================================================================
    // update() — switch indikator
    // =========================================================================

    // F35 - Switch indikator via service → lama nonaktif, baru aktif di DB
    public function test_superadmin_can_switch_indikator_via_service()
    {
        $oldIr = IndikatorRuangan::create([
            'id_ruangan' => 'R01',
            'id_indikator' => $this->im1->id_indikator,
            'active' => true,
        ]);

        $this->app->make(\App\Services\MutuService::class)->switchIndikatorRuangan(
            'R01',
            $oldIr->id_indikator_ruangan,
            $this->im2->id_indikator
        );

        $this->assertDatabaseHas('indikator_ruangan', [
            'id_indikator_ruangan' => $oldIr->id_indikator_ruangan,
            'active' => false,
        ]);
        $this->assertDatabaseHas('indikator_ruangan', [
            'id_ruangan' => 'R01',
            'id_indikator' => $this->im2->id_indikator,
            'active' => true,
        ]);
    }

    // F-GAP-11 - Switch indikator via HTTP POST route → redirect success, DB berubah
    public function test_superadmin_can_switch_indikator_via_http_route()
    {
        $oldIr = IndikatorRuangan::create([
            'id_ruangan' => 'R01',
            'id_indikator' => $this->im1->id_indikator,
            'active' => true,
        ]);

        $response = $this->actingAs($this->superadmin)
            ->post(route('superadmin.ruangan.update_indikator'), [
                'id_ruangan' => 'R01',
                'id_indikator_ruangan_lama' => $oldIr->id_indikator_ruangan,
                'id_indikator_baru' => $this->im2->id_indikator,
            ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('indikator_ruangan', [
            'id_indikator_ruangan' => $oldIr->id_indikator_ruangan,
            'active' => false,
        ]);
        $this->assertDatabaseHas('indikator_ruangan', [
            'id_ruangan' => 'R01',
            'id_indikator' => $this->im2->id_indikator,
            'active' => true,
        ]);
    }

    // =========================================================================
    // deactivate() — nonaktifkan indikator ruangan
    // =========================================================================

    // F34 - Deactivate indikator ruangan via service → active=false di DB
    public function test_superadmin_can_deactivate_indikator_via_service()
    {
        $ir = IndikatorRuangan::create([
            'id_ruangan' => 'R01',
            'id_indikator' => $this->im1->id_indikator,
            'active' => true,
        ]);

        $this->app->make(\App\Services\MutuService::class)
            ->deactivateIndikator($ir->id_indikator_ruangan);

        $this->assertDatabaseHas('indikator_ruangan', [
            'id_indikator_ruangan' => $ir->id_indikator_ruangan,
            'active' => false,
        ]);
    }

    // F-GAP-12 - Deactivate indikator via HTTP POST route → redirect success, active=false DB
    public function test_superadmin_can_deactivate_indikator_via_http_route()
    {
        $ir = IndikatorRuangan::create([
            'id_ruangan' => 'R01',
            'id_indikator' => $this->im1->id_indikator,
            'active' => true,
        ]);

        $response = $this->actingAs($this->superadmin)
            ->post(route('superadmin.ruangan.deactivate_indikator'), [
                'id_indikator_ruangan' => $ir->id_indikator_ruangan,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('indikator_ruangan', [
            'id_indikator_ruangan' => $ir->id_indikator_ruangan,
            'active' => false,
        ]);
    }
}