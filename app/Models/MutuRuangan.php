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
        'id_indikator_ruangan', 
        'total_pasien',
        'pasien_sesuai'
    ];

    public function indikatorRuangan()
    {
        return $this->belongsTo(IndikatorRuangan::class, 'id_indikator_ruangan', 'id_indikator_ruangan');
    }
}