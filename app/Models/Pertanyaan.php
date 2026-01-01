<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pertanyaan extends Model
{
    protected $table = 'pertanyaan';
    protected $primaryKey = 'id_pertanyaan';
    public $timestamps = false;

    protected $fillable = ['pertanyaan', 'urutan'];

    // Relasi: Satu pertanyaan punya banyak pilihan jawaban
    public function pilihanJawaban()
    {
        return $this->hasMany(PilihanJawaban::class, 'id_pertanyaan');
    }
}