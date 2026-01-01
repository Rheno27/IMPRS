<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PilihanJawaban extends Model
{
    protected $table = 'pilihan_jawaban';
    protected $primaryKey = 'id_pilihan';
    public $timestamps = false;

    protected $fillable = ['id_pertanyaan', 'pilihan', 'nilai'];

    public function pertanyaan()
    {
        return $this->belongsTo(Pertanyaan::class, 'id_pertanyaan');
    }
}