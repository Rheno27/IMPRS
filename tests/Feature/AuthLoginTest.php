<?php

namespace Tests\Feature;

use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthLoginTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        // Pastikan ruangan SP00 ada sebagai FK superadmin
        Ruangan::firstOrCreate(['id_ruangan' => 'SP00'], ['nama_ruangan' => 'Superadmin']);
        Ruangan::firstOrCreate(['id_ruangan' => 'R01'], ['nama_ruangan' => 'Ruangan A']);
    }

    // F01 - Halaman login dapat diakses
    public function test_login_page_is_accessible()
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);
    }

    // F02 - Superadmin login redirect ke /superadmin/dashboard
    public function test_superadmin_login_redirects_to_superadmin_dashboard()
    {
        $user = User::create([
            'id_user'      => 'SP001',
            'id_ruangan'   => 'SP00',
            'username'     => 'superadmin',
            'password'     => Hash::make('password'),
            'nama_ruangan' => 'Super Admin',
        ]);

        $response = $this->post(route('login'), [
            'username' => 'superadmin',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('superadmin.dashboard'));
    }

    // F03 - Admin login redirect ke /admin/dashboard
    public function test_admin_login_redirects_to_admin_dashboard()
    {
        $user = User::create([
            'id_user'      => 'U001',
            'id_ruangan'   => 'R01',
            'username'     => 'adminruangan',
            'password'     => Hash::make('password'),
            'nama_ruangan' => 'Ruangan A',
        ]);

        $response = $this->post(route('login'), [
            'username' => 'adminruangan',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
    }

    // F04 - Login gagal dengan password salah
    public function test_login_fails_with_wrong_password()
    {
        User::create([
            'id_user'      => 'U001',
            'id_ruangan'   => 'R01',
            'username'     => 'adminruangan',
            'password'     => Hash::make('password'),
            'nama_ruangan' => 'Ruangan A',
        ]);

        $response = $this->post(route('login'), [
            'username' => 'adminruangan',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    // F05 - Login gagal dengan username tidak ada
    public function test_login_fails_with_nonexistent_username()
    {
        $response = $this->post(route('login'), [
            'username' => 'tidakada',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    // F06 - Login gagal dengan field kosong
    public function test_login_fails_with_empty_fields()
    {
        $response = $this->post(route('login'), [
            'username' => '',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['username', 'password']);
    }

    // F07 - User yang sudah login di-redirect dari halaman login
    public function test_authenticated_user_is_redirected_from_login_page()
    {
        $user = User::create([
            'id_user'      => 'U001',
            'id_ruangan'   => 'R01',
            'username'     => 'admin',
            'password'     => Hash::make('password'),
            'nama_ruangan' => 'Ruangan A',
        ]);

        $response = $this->actingAs($user)->get(route('login'));
        $response->assertRedirect();
    }

    // F08 - Logout menghapus session
    public function test_logout_invalidates_session()
    {
        $user = User::create([
            'id_user'      => 'U001',
            'id_ruangan'   => 'R01',
            'username'     => 'admin',
            'password'     => Hash::make('password'),
            'nama_ruangan' => 'Ruangan A',
        ]);

        $this->actingAs($user)->post(route('logout'));
        $this->assertGuest();
    }
}
