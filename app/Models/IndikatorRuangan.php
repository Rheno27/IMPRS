<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndikatorRuangan extends Model
{
    protected $table = 'indikator_ruangan';
    protected $primaryKey = 'id_indikator_ruangan';
    public $incrementing = true;
    public $timestamps = false;

    /**
     * Kolom yang bisa diisi (mass assignable).
     * Sesuai dengan skema database.
     */
    protected $fillable = [
        'id_ruangan',
        'id_indikator',
        'active'
    ];

    /**
     * Mendefinisikan relasi "belongsTo" ke model IndikatorMutu.
     * Setiap record IndikatorRuangan memiliki satu IndikatorMutu.
     */
    public function indikatorMutu()
    {
        return $this->belongsTo(IndikatorMutu::class, 'id_indikator', 'id_indikator');
    }

    /**
     * Mendefinisikan relasi "belongsTo" ke model Ruangan.
     * Setiap record IndikatorRuangan dimiliki oleh satu Ruangan.
     */
    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'id_ruangan', 'id_ruangan');
    }

    /**
     * Mendefinisikan relasi "hasMany" ke model MutuRuangan.
     * Setiap record IndikatorRuangan bisa memiliki banyak data MutuRuangan.
     */
    public function mutuRuangan()
    {
        return $this->hasMany(MutuRuangan::class, 'id_indikator_ruangan', 'id_indikator_ruangan');
    }
}