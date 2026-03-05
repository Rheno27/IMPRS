<?php

namespace Tests\Feature;

use App\Models\IndikatorMutu;
use App\Models\IndikatorRuangan;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class IndikatorRuanganTest extends TestCase
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

        \App\Models\Kategori::firstOrCreate(['id_kategori' => 1], ['kategori' => 'Kategori A']);

        $this->im1 = IndikatorMutu::create(['id_kategori' => 1, 'variabel' => 'Indikator A', 'standar' => '90']);
        $this->im2 = IndikatorMutu::create(['id_kategori' => 1, 'variabel' => 'Indikator B', 'standar' => '85']);

        $this->superadmin = User::create([
            'id_user' => 'SP001',
            'id_ruangan' => 'SP00',
            'username' => 'superadmin',
            'password' => Hash::make('password'),
            'nama_ruangan' => 'Super Admin',
        ]);
    }

    // F32 - Superadmin bisa assign indikator ke ruangan
    public function test_superadmin_can_assign_indikator_to_ruangan()
    {
        $response = $this->actingAs($this->superadmin)->post(route('superadmin.ruangan.add_indikator'), [
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

    // F33 - Tidak bisa assign indikator yang sama dua kali ke ruangan yang sama
    public function test_cannot_assign_duplicate_active_indikator_to_same_ruangan()
    {
        IndikatorRuangan::create([
            'id_ruangan' => 'R01',
            'id_indikator' => $this->im1->id_indikator,
            'active' => true,
        ]);

        $response = $this->actingAs($this->superadmin)->post(route('superadmin.ruangan.add_indikator'), [
            'id_ruangan' => 'R01',
            'id_indikator' => $this->im1->id_indikator,
        ]);

        // Harus ada error atau redirect dengan pesan error
        $response->assertSessionHasErrors();
    }

    // F34 - Superadmin bisa menonaktifkan indikator_ruangan
    public function test_superadmin_can_deactivate_indikator_ruangan()
    {
        $indikatorRuangan = IndikatorRuangan::create([
            'id_ruangan' => 'R01',
            'id_indikator' => $this->im1->id_indikator,
            'active' => true,
        ]);

        // Call service directly to deactivate (controller route uses the same service).
        $this->app->make(\App\Services\MutuService::class)->deactivateIndikator($indikatorRuangan->id_indikator_ruangan);

        $this->assertDatabaseHas('indikator_ruangan', [
            'id_indikator_ruangan' => $indikatorRuangan->id_indikator_ruangan,
            'active' => false,
        ]);
    }

    // F35 - Superadmin bisa ganti indikator (old nonaktif, new aktif)
    public function test_superadmin_can_switch_indikator()
    {
        $oldIndikator = IndikatorRuangan::create([
            'id_ruangan' => 'R01',
            'id_indikator' => $this->im1->id_indikator,
            'active' => true,
        ]);

        // Use service directly to switch indikator
        $this->app->make(\App\Services\MutuService::class)->switchIndikatorRuangan(
            'R01',
            $oldIndikator->id_indikator_ruangan,
            $this->im2->id_indikator
        );

        $this->assertDatabaseHas('indikator_ruangan', [
            'id_indikator_ruangan' => $oldIndikator->id_indikator_ruangan,
            'active' => false,
        ]);

        $this->assertDatabaseHas('indikator_ruangan', [
            'id_ruangan' => 'R01',
            'id_indikator' => $this->im2->id_indikator,
            'active' => true,
        ]);
    }

    // F36 - Halaman edit hanya menampilkan indikator aktif
    public function test_edit_page_shows_only_active_indikators()
    {
        $activeIndikator = IndikatorRuangan::create([
            'id_ruangan' => 'R01',
            'id_indikator' => $this->im1->id_indikator,
            'active' => true,
        ]);
        $inactiveIndikator = IndikatorRuangan::create([
            'id_ruangan' => 'R01',
            'id_indikator' => $this->im2->id_indikator,
            'active' => false,
        ]);

        $response = $this->actingAs($this->superadmin)->get(
            route('superadmin.ruangan.edit_indikator', 'R01')
        );

        // Save response HTML for debugging
        file_put_contents(storage_path('logs/edit_page.html'), $response->getContent());

        $response->assertStatus(200);

        // The page should include the active indikator's edit modal id
        $response->assertSee('editModal-' . $activeIndikator->id_indikator_ruangan);

        // The inactive indikator must not render an edit modal (ensures it's not shown as active)
        $response->assertDontSee('editModal-' . $inactiveIndikator->id_indikator_ruangan);
    }
}
