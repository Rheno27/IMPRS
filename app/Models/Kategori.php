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
    
    public function indikatorMutus()
    {
        // Parameter kedua ('id_kategori') adalah foreign key di tabel 'indikator_mutu'
        return $this->hasMany(IndikatorMutu::class, 'id_kategori');
    }
}