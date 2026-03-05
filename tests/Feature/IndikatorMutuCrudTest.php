<?php

namespace Tests\Feature;

use App\Models\IndikatorMutu;
use App\Models\IndikatorRuangan;
use App\Models\Kategori;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class IndikatorMutuCrudTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superadmin;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Ruangan::firstOrCreate(['id_ruangan' => 'SP00'], ['nama_ruangan' => 'Super Admin']);
        Ruangan::firstOrCreate(['id_ruangan' => 'R01'], values: ['nama_ruangan' => 'Nifas']);

        Kategori::firstOrCreate(['id_kategori' => 1], ['nama_kategori' => 'Kategori A']);

        $this->superadmin = User::create([
            'id_user' => 'SP001',
            'id_ruangan' => 'SP00',
            'username' => 'superadmin',
            'password' => Hash::make('superadmin123'),
            'nama_ruangan' => 'Super Admin',
        ]);

        $this->admin = User::create([
            'id_user' => 'U001',
            'id_ruangan' => 'R01',
            'username' => 'ruang_nifas',
            'password' => Hash::make('nifas123'),
            'nama_ruangan' => 'Nifas',
        ]);
    }

    // F24 - Superadmin bisa melihat daftar indikator
    public function test_superadmin_can_view_indikator_list()
    {
        $response = $this->actingAs($this->superadmin)->get(route('superadmin.indikator_mutu.create'));
        $response->assertStatus(200);
    }

    // F25 - Superadmin bisa membuat IndikatorMutu baru
    public function test_superadmin_can_create_indikator_mutu()
    {
        $response = $this->actingAs($this->superadmin)->post(route('superadmin.indikator_mutu.store'), [
            'id_kategori' => 1,
            'variabel' => 'Variabel Baru',
            'standar' => '90',
            'satuan' => 'Persen',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('indikator_mutu', [
            'variabel' => 'Variabel Baru',
            'standar' => 90,
        ]);
    }

    // F26 - Gagal membuat indikator tanpa variabel
    public function test_create_fails_without_variabel()
    {
        $response = $this->actingAs($this->superadmin)->post(route('superadmin.indikator_mutu.store'), [
            'id_kategori' => 1,
            'standar' => 90,
        ]);

        $response->assertSessionHasErrors('variabel');
    }

    // F27 - Gagal membuat indikator tanpa standar
    public function test_create_fails_without_standar()
    {
        $response = $this->actingAs($this->superadmin)->post(route('superadmin.indikator_mutu.store'), [
            'id_kategori' => 1,
            'variabel' => 'Variabel Baru',
        ]);

        $response->assertSessionHasErrors('standar');
    }

    // F28 - Superadmin bisa mengupdate IndikatorMutu
    public function test_superadmin_can_update_indikator_mutu()
    {
        $indikator = IndikatorMutu::create([
            'id_kategori' => 1,
            'variabel' => 'Variabel Lama',
            'standar' => '80',
        ]);

        $response = $this->actingAs($this->superadmin)->put(route('superadmin.indikator_mutu.update', $indikator->id_indikator), [
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

    // F29 - Superadmin bisa menghapus IndikatorMutu yang tidak digunakan
    public function test_superadmin_can_delete_unused_indikator_mutu()
    {
        $indikator = IndikatorMutu::create([
            'id_kategori' => 1,
            'variabel' => 'Akan Dihapus',
            'standar' => '80',
        ]);

        $response = $this->actingAs($this->superadmin)->delete(route('superadmin.indikator_mutu.destroy', $indikator->id_indikator));

        $response->assertRedirect();
        $this->assertDatabaseMissing('indikator_mutu', ['id_indikator' => $indikator->id_indikator]);
    }

    // F30 - Tidak bisa menghapus IndikatorMutu yang sedang digunakan
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

        $response = $this->actingAs($this->superadmin)->delete(route('superadmin.indikator_mutu.destroy', $indikator->id_indikator));

        // Harus gagal - masih ada relasi
        $this->assertDatabaseHas('indikator_mutu', ['id_indikator' => $indikator->id_indikator]);
    }

    // F31 - Admin tidak bisa akses management indikator (403)
    public function test_admin_cannot_access_indikator_management()
    {
        $response = $this->actingAs($this->admin)->get(route('superadmin.indikator_mutu.create'));
        $response->assertStatus(403);
    }
}
