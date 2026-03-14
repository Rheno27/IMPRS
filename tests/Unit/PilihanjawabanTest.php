<?php

namespace Tests\Unit\Models;

use App\Models\Pertanyaan;
use App\Models\PilihanJawaban;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PilihanJawabanTest extends TestCase
{
    use DatabaseTransactions;

    // U35 - belongsTo Pertanyaan — instanceof BelongsTo
    public function test_belongs_to_pertanyaan_relation()
    {
        $pilihan = new PilihanJawaban();
        $relation = $pilihan->pertanyaan();

        $this->assertInstanceOf(BelongsTo::class, $relation);
    }

    // U36 - fillable mengandung id_pilihan, id_pertanyaan, pilihan, nilai
    public function test_fillable_contains_required_attributes()
    {
        $pilihan = new PilihanJawaban();
        $fillable = $pilihan->getFillable();

        $this->assertContains('id_pilihan', $fillable);
        $this->assertContains('id_pertanyaan', $fillable);
        $this->assertContains('pilihan', $fillable);
        $this->assertContains('nilai', $fillable);
    }

    // U37 - timestamps = false
    public function test_timestamps_are_disabled()
    {
        $pilihan = new PilihanJawaban();
        $this->assertFalse($pilihan->timestamps);
    }

    // U37b - relasi ke Pertanyaan terbaca dari DB
    public function test_relation_to_pertanyaan_loads_from_database()
    {
        Pertanyaan::firstOrCreate(
            ['id_pertanyaan' => 1],
            ['pertanyaan' => 'Bagaimana pelayanan kami?', 'urutan' => 1]
        );

        $pilihan = PilihanJawaban::firstOrCreate(
            ['id_pilihan' => 1],
            ['id_pertanyaan' => 1, 'pilihan' => 'Sangat Baik', 'nilai' => 4]
        );

        $this->assertInstanceOf(Pertanyaan::class, $pilihan->pertanyaan);
        $this->assertEquals(1, $pilihan->pertanyaan->id_pertanyaan);
    }
}