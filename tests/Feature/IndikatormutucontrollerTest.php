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

class IndikatorMutuControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superadmin;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Ruangan::firstOrCreate(['id_ruangan' => 'SP00'], ['nama_ruangan' => 'Super Admin']);
        Ruangan::firstOrCreate(['id_ruangan' => 'R01'], ['nama_ruangan' => 'Nifas']);
        Kategori::firstOrCreate(['id_kategori' => 1], ['kategori' => 'Indikator Nasional Mutu']);

        $this->superadmin = User::create([
            'id_user' => 'SP001',
            'id_ruangan' => 'SP00',
            'username' => 'superadmin_im',
            'password' => Hash::make('password'),
            'nama_ruangan' => 'Super Admin',
        ]);

        $this->admin = User::create([
            'id_user' => 'U001',
            'id_ruangan' => 'R01',
            'username' => 'admin_im',
            'password' => Hash::make('password'),
            'nama_ruangan' => 'Nifas',
        ]);
    }

    // =========================================================================
    // IndikatorMutuController@create
    // =========================================================================

    // F24 - Superadmin bisa melihat daftar indikator (200)
    public function test_superadmin_can_view_indikator_list()
    {
        $response = $this->actingAs($this->superadmin)->get(route('superadmin.indikator_mutu.create'));
        $response->assertStatus(200);
    }

    // F31 - Admin tidak bisa akses manajemen indikator (403)
    public function test_admin_cannot_access_indikator_management()
    {
        $response = $this->actingAs($this->admin)->get(route('superadmin.indikator_mutu.create'));
        $response->assertStatus(403);
    }

    // =========================================================================
    // IndikatorMutuController@store
    // =========================================================================

    // F25 - Superadmin bisa membuat IndikatorMutu baru → assertDatabaseHas
    public function test_superadmin_can_create_indikator_mutu()
    {
        $response = $this->actingAs($this->superadmin)->post(route('superadmin.indikator_mutu.store'), [
            'id_kategori' => 1,
            'variabel' => 'Variabel Baru Test',
            'standar' => '90',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('indikator_mutu', [
            'variabel' => 'Variabel Baru Test',
            'standar' => '90',
        ]);
    }

    // F26 - Gagal membuat indikator tanpa variabel → sessionHasErrors('variabel')
    public function test_create_fails_without_variabel()
    {
        $response = $this->actingAs($this->superadmin)->post(route('superadmin.indikator_mutu.store'), [
            'id_kategori' => 1,
            'standar' => '90',
        ]);

        $response->assertSessionHasErrors('variabel');
    }

    // F27 - Gagal membuat indikator tanpa standar → sessionHasErrors('standar')
    public function test_create_fails_without_standar()
    {
        $response = $this->actingAs($this->superadmin)->post(route('superadmin.indikator_mutu.store'), [
            'id_kategori' => 1,
            'variabel' => 'Variabel Test',
        ]);

        $response->assertSessionHasErrors('standar');
    }

    // =========================================================================
    // IndikatorMutuController@update
    // =========================================================================

    // F28 - Superadmin bisa update IndikatorMutu → assertDatabaseHas perubahan
    public function test_superadmin_can_update_indikator_mutu()
    {
        $indikator = IndikatorMutu::create([
            'id_kategori' => 1,
            'variabel' => 'Variabel Lama',
            'standar' => '80',
        ]);

        $response = $this->actingAs($this->superadmin)
            ->put(route('superadmin.indikator_mutu.update', $indikator->id_indikator), [
                'id_kategori' => 1,
                'variabel' => 'Variabel Baru',
                'standar' => '95',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('indikator_mutu', [
            'id_indikator' => $indikator->id_indikator,
            'variabel' => 'Variabel Baru',
            'standar' => '95',
        ]);
    }

    // =========================================================================
    // IndikatorMutuController@destroy
    // =========================================================================

    // F29 - Superadmin bisa hapus IndikatorMutu yang tidak digunakan
    public function test_superadmin_can_delete_unused_indikator_mutu()
    {
        $indikator = IndikatorMutu::create([
            'id_kategori' => 1,
            'variabel' => 'Akan Dihapus',
            'standar' => '80',
        ]);

        $response = $this->actingAs($this->superadmin)
            ->delete(route('superadmin.indikator_mutu.destroy', $indikator->id_indikator));

        $response->assertRedirect();
        $this->assertDatabaseMissing('indikator_mutu', ['id_indikator' => $indikator->id_indikator]);
    }

    // F30 - Tidak bisa hapus IndikatorMutu yang sedang dipakai ruangan
    public function test_cannot_delete_indikator_mutu_in_use()
    {
        $indikator = IndikatorMutu::create([
            'id_kategori' => 1,
            'variabel' => 'Sedang Dipakai',
            'standar' => '80',
        ]);

        IndikatorRuangan::create([
            'id_ruangan' => 'R01',
            'id_indikator' => $indikator->id_indikator,
            'active' => true,
        ]);

        $this->actingAs($this->superadmin)
            ->delete(route('superadmin.indikator_mutu.destroy', $indikator->id_indikator));

        $this->assertDatabaseHas('indikator_mutu', ['id_indikator' => $indikator->id_indikator]);
    }
}
