<?php

namespace Tests\Unit\Models;

use App\Models\IndikatorMutu;
use App\Models\Kategori;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class IndikatorMutuTest extends TestCase
{
    use DatabaseTransactions;

    // U08 - belongsTo Kategori — instanceof BelongsTo dan relasi terbaca
    public function test_belongs_to_kategori_relation()
    {
        $kategori = Kategori::firstOrCreate(
            ['id_kategori' => 1],
            ['kategori' => 'Indikator Nasional Mutu']
        );

        $indikator = IndikatorMutu::create([
            'id_kategori' => $kategori->id_kategori,
            'variabel' => 'Kepatuhan Kebersihan Tangan',
            'standar' => '85',
        ]);

        // Verifikasi tipe relasi
        $this->assertInstanceOf(BelongsTo::class, $indikator->kategori());
        // Verifikasi relasi terbaca dan FK cocok — tidak assert nama spesifik
        // karena data kategori bisa berbeda antar environment
        $this->assertInstanceOf(Kategori::class, $indikator->kategori);
        $this->assertEquals($kategori->id_kategori, $indikator->kategori->id_kategori);
    }

    // U09 - hasMany IndikatorRuangan — instanceof HasMany
    public function test_has_many_indikator_ruangan_relation()
    {
        $indikator = new IndikatorMutu();
        $relation = $indikator->indikatorRuangan();

        $this->assertInstanceOf(HasMany::class, $relation);
    }

    // U10 - fillable mengandung id_indikator, variabel, standar
    public function test_fillable_contains_required_attributes()
    {
        $indikator = new IndikatorMutu();
        $fillable = $indikator->getFillable();

        $this->assertContains('id_indikator', $fillable);
        $this->assertContains('variabel', $fillable);
        $this->assertContains('standar', $fillable);
        $this->assertContains('id_kategori', $fillable);
    }

    // U11 - timestamps = false
    public function test_timestamps_are_disabled()
    {
        $indikator = new IndikatorMutu();
        $this->assertFalse($indikator->timestamps);
    }
}