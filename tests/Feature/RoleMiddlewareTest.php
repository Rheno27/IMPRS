<?php

namespace Tests\Feature;

use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected User $superadmin;

    protected function setUp(): void
    {
        parent::setUp();

        Ruangan::firstOrCreate(['id_ruangan' => 'SP00'], ['nama_ruangan' => 'Superadmin']);
        Ruangan::firstOrCreate(['id_ruangan' => 'R01'], ['nama_ruangan' => 'Ruangan A']);

        $this->admin = User::create([
            'id_user'      => 'U001',
            'id_ruangan'   => 'R01',
            'username'     => 'adminruangan',
            'password'     => Hash::make('password'),
            'nama_ruangan' => 'Ruangan A',
        ]);

        $this->superadmin = User::create([
            'id_user'      => 'SP001',
            'id_ruangan'   => 'SP00',
            'username'     => 'superadmin',
            'password'     => Hash::make('password'),
            'nama_ruangan' => 'Super Admin',
        ]);
    }

    // F09 - Admin bisa akses /admin/dashboard
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

    // F11 - Admin bisa akses /admin/input_indikator
    public function test_admin_can_access_input_indikator()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.input_indikator'));
        $response->assertStatus(200);
    }

    // F12 - Superadmin bisa akses /superadmin/dashboard
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

    // F14 - Unauthenticated redirect dari admin route
    public function test_unauthenticated_redirected_from_admin_route()
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('login'));
    }

    // F15 - Unauthenticated redirect dari superadmin route
    public function test_unauthenticated_redirected_from_superadmin_route()
    {
        $response = $this->get(route('superadmin.dashboard'));
        $response->assertRedirect(route('login'));
    }

    // F16 - Superadmin bisa akses halaman rekap SKM
    public function test_superadmin_can_access_skm_rekap()
    {
        $response = $this->actingAs($this->superadmin)->get(route('superadmin.skm.rekap'));
        $response->assertStatus(200);
    }
}
