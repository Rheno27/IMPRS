<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MutuRuangan extends Model
{
    protected $table = 'mutu_ruangan';
    protected $primaryKey = 'id_mutu';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'tanggal',
        'id_ruangan',
        'id_indikator',
        'total_pasien',
        'pasien_sesuai'
    ];

    public function indikator()
    {
        return $this->belongsTo(IndikatorMutu::class, 'id_indikator', 'id_indikator');
    }
}
