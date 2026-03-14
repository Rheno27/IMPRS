<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    // U01 - isSuperadmin() return true jika id_ruangan = 'SP00'
    public function test_is_superadmin_returns_true_when_id_ruangan_is_SP00()
    {
        $user = new User(['id_ruangan' => 'SP00']);
        $this->assertTrue($user->isSuperadmin());
    }

    // U02 - isSuperadmin() return false jika id_ruangan != 'SP00'
    public function test_is_superadmin_returns_false_when_id_ruangan_is_not_SP00()
    {
        $user = new User(['id_ruangan' => 'R01']);
        $this->assertFalse($user->isSuperadmin());
    }

    // U03 - isAdminRuangan() return true jika id_ruangan != 'SP00'
    public function test_is_admin_ruangan_returns_true_when_id_ruangan_is_not_SP00()
    {
        $user = new User(['id_ruangan' => 'R01']);
        $this->assertTrue($user->isAdminRuangan());
    }

    // U04 - isAdminRuangan() return false jika id_ruangan = 'SP00'
    public function test_is_admin_ruangan_returns_false_when_id_ruangan_is_SP00()
    {
        $user = new User(['id_ruangan' => 'SP00']);
        $this->assertFalse($user->isAdminRuangan());
    }

    // U05 - fillable mengandung id_user, id_ruangan, username, password
    public function test_fillable_contains_required_attributes()
    {
        $user = new User();
        $fillable = $user->getFillable();

        $this->assertContains('id_user', $fillable);
        $this->assertContains('id_ruangan', $fillable);
        $this->assertContains('username', $fillable);
        $this->assertContains('password', $fillable);
    }

    // U06 - timestamps = false
    public function test_timestamps_are_disabled()
    {
        $user = new User();
        $this->assertFalse($user->timestamps);
    }

    // U07 - incrementing = false dan keyType = 'string'
    public function test_primary_key_is_non_incrementing_string()
    {
        $user = new User();
        $this->assertFalse($user->incrementing);
        $this->assertEquals('string', $user->getKeyType());
    }
}