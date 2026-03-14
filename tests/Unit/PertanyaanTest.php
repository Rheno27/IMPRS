<?php

namespace Tests\Unit\Models;

use App\Models\Pertanyaan;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PertanyaanTest extends TestCase
{
    use DatabaseTransactions;

    // U32 - hasMany PilihanJawaban — instanceof HasMany
    public function test_has_many_pilihan_jawaban_relation()
    {
        $pertanyaan = new Pertanyaan();
        $relation = $pertanyaan->pilihanJawaban();

        $this->assertInstanceOf(HasMany::class, $relation);
    }

    // U33 - fillable mengandung id_pertanyaan, pertanyaan, urutan
    public function test_fillable_contains_required_attributes()
    {
        $pertanyaan = new Pertanyaan();
        $fillable = $pertanyaan->getFillable();

        $this->assertContains('id_pertanyaan', $fillable);
        $this->assertContains('pertanyaan', $fillable);
        $this->assertContains('urutan', $fillable);
    }

    // U34 - timestamps = false
    public function test_timestamps_are_disabled()
    {
        $pertanyaan = new Pertanyaan();
        $this->assertFalse($pertanyaan->timestamps);
    }

    // U34b - relasi pilihanJawaban terbaca dari DB
    public function test_pilihan_jawaban_loaded_from_database()
    {
        $pertanyaan = Pertanyaan::firstOrCreate(
            ['id_pertanyaan' => 1],
            ['pertanyaan' => 'Bagaimana pelayanan petugas?', 'urutan' => 1]
        );

        \App\Models\PilihanJawaban::firstOrCreate(
            ['id_pilihan' => 1],
            ['id_pertanyaan' => 1, 'pilihan' => 'Sangat Baik', 'nilai' => 4]
        );

        $this->assertGreaterThan(0, $pertanyaan->pilihanJawaban()->count());
    }
}