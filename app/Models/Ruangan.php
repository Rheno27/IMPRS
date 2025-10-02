<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    protected $table = 'ruangan';
    protected $primaryKey = 'id_ruangan';

    /**
     * Karena primary key (id_ruangan) adalah VARCHAR (contoh: 'R01'),
     * kita harus set $incrementing menjadi false.
     */
    public $incrementing = false;

    /**
     * Dan kita definisikan tipe datanya sebagai string.
     */
    protected $keyType = 'string';

    public $timestamps = false;

    /**
     * Kolom yang bisa diisi (mass assignable).
     */
    protected $fillable = [
        'id_ruangan',
        'nama_ruangan'
    ];

    /**
     * Mendefinisikan relasi "hasMany" ke model IndikatorRuangan.
     * Satu Ruangan bisa memiliki banyak IndikatorRuangan.
     */
    public function indikatorRuangan()
    {
        return $this->hasMany(IndikatorRuangan::class, 'id_ruangan', 'id_ruangan');
    }
}
