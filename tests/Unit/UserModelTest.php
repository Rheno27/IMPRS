<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use DatabaseTransactions;

    // U01 - isSuperadmin() returns true when id_ruangan = 'SP00'
    public function test_is_superadmin_returns_true_when_id_ruangan_is_SP00()
    {
        $user = new User(['id_ruangan' => 'SP00']);
        $this->assertTrue($user->isSuperadmin());
    }

    // U02 - isSuperadmin() returns false when id_ruangan != 'SP00'
    public function test_is_superadmin_returns_false_when_id_ruangan_is_not_SP00()
    {
        $user = new User(['id_ruangan' => 'R01']);
        $this->assertFalse($user->isSuperadmin());
    }

    // U03 - isAdminRuangan() returns true when id_ruangan != 'SP00'
    public function test_is_admin_ruangan_returns_true_when_id_ruangan_is_not_SP00()
    {
        $user = new User(['id_ruangan' => 'R01']);
        $this->assertTrue($user->isAdminRuangan());
    }

    // U04 - isAdminRuangan() returns false when id_ruangan = 'SP00'
    public function test_is_admin_ruangan_returns_false_when_id_ruangan_is_SP00()
    {
        $user = new User(['id_ruangan' => 'SP00']);
        $this->assertFalse($user->isAdminRuangan());
    }

    // U05 - Fillable attributes are correctly defined
    public function test_user_fillable_attributes()
    {
        $user = new User();
        $fillable = $user->getFillable();

        $this->assertContains('id_user', $fillable);
        $this->assertContains('id_ruangan', $fillable);
        $this->assertContains('username', $fillable);
        $this->assertContains('password', $fillable);
    }
}
