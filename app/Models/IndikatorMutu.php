<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndikatorMutu extends Model
{
    protected $table = 'indikator_mutu';
    protected $primaryKey = 'id_indikator';
    public $incrementing = true;
    public $timestamps = false;
}