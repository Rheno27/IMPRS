<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BioPasien extends Model
{
    protected $table = 'bio_pasien';
    protected $primaryKey = 'id_pasien';
    public $timestamps = false;

    protected $fillable = [
        'id_ruangan',
        'no_rm',
        'umur',
        'jenis_kelamin',
        'pendidikan',
        'pekerjaan'
    ];
}