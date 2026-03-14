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

class RoleAccessTest extends TestCase
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
            'variabel' => 'Indikator Role Test',
            'standar' => '90',
        ]);

        $ir = IndikatorRuangan::create([
            'id_ruangan' => 'R01',
            'id_indikator' => $indikator->id_indikator,
            'active' => true,
        ]);

        MutuRuangan::create([
            'id_indikator_ruangan' => $ir->id_indikator_ruangan,
            'tanggal' => now()->format('Y-m-d'),
            'pasien_sesuai' => 8,
            'total_pasien' => 10,
        ]);

        $this->admin = User::create([
            'id_user' => 'U001',
            'id_ruangan' => 'R01',
            'username' => 'adminruangan',
            'password' => Hash::make('password'),
            'nama_ruangan' => 'Ruangan A',
        ]);

        $this->superadmin = User::create([
            'id_user' => 'SP001',
            'id_ruangan' => 'SP00',
            'username' => 'superadmin_role',
            'password' => Hash::make('password'),
            'nama_ruangan' => 'Super Admin',
        ]);
    }

    // F09 - Admin bisa akses /admin/dashboard (200)
    public function test_admin_can_access_admin_dashboard()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
    }

    // F10 - Admin tidak bisa akses /superadmin/dashboard (403)
    public function test_admin_cannot_access_superadmin_dashboard()
    {
        $response = $this->actingAs($this->admin)->get(route('superadmin.dashboard'));
        $response->assertStatus(403);
    }

    // F11 - Admin bisa akses /admin/input_indikator (200)
    public function test_admin_can_access_input_indikator()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.input_indikator'));
        $response->assertStatus(200);
    }

    // F12 - Superadmin bisa akses /superadmin/dashboard (200)
    public function test_superadmin_can_access_superadmin_dashboard()
    {
        $response = $this->actingAs($this->superadmin)->get(route('superadmin.dashboard'));
        $response->assertStatus(200);
    }

    // F13 - Superadmin tidak bisa akses /admin/dashboard (403)
    public function test_superadmin_cannot_access_admin_dashboard()
    {
        $response = $this->actingAs($this->superadmin)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    // F14 - Guest redirect dari admin route → route('login')
    public function test_unauthenticated_redirected_from_admin_route()
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('login'));
    }

    // F15 - Guest redirect dari superadmin route → route('login')
    public function test_unauthenticated_redirected_from_superadmin_route()
    {
        $response = $this->get(route('superadmin.dashboard'));
        $response->assertRedirect(route('login'));
    }

    // F16 - Superadmin bisa akses halaman rekap SKM (200)
    public function test_superadmin_can_access_skm_rekap()
    {
        $response = $this->actingAs($this->superadmin)->get(route('superadmin.skm.rekap'));
        $response->assertStatus(200);
    }
}