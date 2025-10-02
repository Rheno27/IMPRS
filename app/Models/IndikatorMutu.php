<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndikatorMutu extends Model
{
    protected $table = 'indikator_mutu';
    protected $primaryKey = 'id_indikator';
    public $incrementing = true;
    public $timestamps = false;

    /**
     * Kolom yang bisa diisi (mass assignable).
     */
    protected $fillable = [
        'id_kategori',
        'variabel',
        'standar'
    ];

    /**
     * Mendefinisikan relasi "hasMany" ke model IndikatorRuangan.
     * Satu IndikatorMutu bisa dimiliki oleh banyak IndikatorRuangan.
     */
    public function indikatorRuangan()
    {
        return $this->hasMany(IndikatorRuangan::class, 'id_indikator', 'id_indikator');
    }

    public function kategori()
    {
        // Parameter kedua ('id_kategori') adalah nama foreign key di tabel 'indikator_mutu'
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }
}