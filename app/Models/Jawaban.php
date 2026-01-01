<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jawaban extends Model
{
    protected $table = 'jawaban';
    protected $primaryKey = 'id_jawaban';
    public $timestamps = false;

    protected $fillable = [
        'tanggal',
        'id_pasien',
        'id_pertanyaan',
        'id_pilihan',
        'hasil_nilai'
    ];

    public function pilihanJawaban()
    {
        return $this->belongsTo(PilihanJawaban::class, 'id_pilihan', 'id_pilihan');
    }

    public function pertanyaan()
    {
        return $this->belongsTo(Pertanyaan::class, 'id_pertanyaan', 'id_pertanyaan');
    }

    public function pasien()
    {
        return $this->belongsTo(BioPasien::class, 'id_pasien', 'id_pasien');
    }
}