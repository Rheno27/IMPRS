<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'user';
    protected $primaryKey = 'id_user';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_user',
        'username',
        'password',
        'id_ruangan',
        'nama_ruangan',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];
    
    public $timestamps = false;

    public function isSuperadmin()
    {
        return $this->id_ruangan === 'SP00';
    }

    public function isAdminRuangan()
    {
        return $this->id_ruangan !== 'SP00';
    }
}