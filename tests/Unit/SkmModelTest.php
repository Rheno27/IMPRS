<?php

namespace Tests\Unit;

use App\Models\BioPasien;
use App\Models\Jawaban;
use App\Models\Pertanyaan;
use App\Models\PilihanJawaban;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SkmModelTest extends TestCase
{
    use DatabaseTransactions;

    // U12 - Pertanyaan hasMany PilihanJawaban
    public function test_pertanyaan_has_many_pilihan_jawaban()
    {
        $pertanyaan = new Pertanyaan();
        $this->assertTrue(method_exists($pertanyaan, 'pilihanJawaban'));

        $relation = $pertanyaan->pilihanJawaban();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $relation);
    }

    // U13 - PilihanJawaban belongsTo Pertanyaan
    public function test_pilihan_jawaban_belongs_to_pertanyaan()
    {
        $pilihan = new PilihanJawaban();
        $this->assertTrue(method_exists($pilihan, 'pertanyaan'));

        $relation = $pilihan->pertanyaan();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
    }

    // U14 - Jawaban belongsTo PilihanJawaban via pilihanJawaban()
    public function test_jawaban_belongs_to_pilihan_jawaban()
    {
        $jawaban = new Jawaban();
        $this->assertTrue(method_exists($jawaban, 'pilihanJawaban'));

        $relation = $jawaban->pilihanJawaban();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
    }

    // U15 - Jawaban belongsTo Pertanyaan
    public function test_jawaban_belongs_to_pertanyaan()
    {
        $jawaban = new Jawaban();
        $this->assertTrue(method_exists($jawaban, 'pertanyaan'));

        $relation = $jawaban->pertanyaan();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
    }

    // U16 - Jawaban belongsTo BioPasien via pasien()
    public function test_jawaban_belongs_to_bio_pasien_via_pasien()
    {
        $jawaban = new Jawaban();
        $this->assertTrue(method_exists($jawaban, 'pasien'));

        $relation = $jawaban->pasien();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
    }

    // U17 - BioPasien fillable attributes
    public function test_bio_pasien_fillable_attributes()
    {
        $bioPasien = new BioPasien();
        $fillable = $bioPasien->getFillable();

        $this->assertContains('id_ruangan', $fillable);
        $this->assertContains('no_rm', $fillable);
        $this->assertContains('umur', $fillable);
        $this->assertContains('jenis_kelamin', $fillable);
        $this->assertContains('pendidikan', $fillable);
        $this->assertContains('pekerjaan', $fillable);
    }
}
