<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    // Mendefinisikan nama tabel secara eksplisit
    protected $table = 'kategori';

    // Mendefinisikan primary key secara eksplisit
    protected $primaryKey = 'id_kategori';
    public $timestamps = false;

    // Allow mass assignment for these fields in tests and controllers
    protected $fillable = [
        'id_kategori',
        'nama_kategori',
        'kategori',
    ];

    public function indikatorMutus()
    {
        // Parameter kedua ('id_kategori') adalah foreign key di tabel 'indikator_mutu'
        return $this->hasMany(IndikatorMutu::class, 'id_kategori');
    }

    // Map virtual attribute `nama_kategori` to the actual DB column `kategori`
    public function setNamaKategoriAttribute($value)
    {
        $this->attributes['kategori'] = $value;
    }

    public function getNamaKategoriAttribute()
    {
        return $this->attributes['kategori'] ?? null;
    }
}