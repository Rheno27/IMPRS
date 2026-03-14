<?php

namespace Tests\Unit\Models;

use App\Models\Kategori;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class KategoriTest extends TestCase
{
    use DatabaseTransactions;

    // U27 - virtual attribute: set nama_kategori → tersimpan di kolom kategori
    public function test_set_nama_kategori_stores_to_kategori_column()
    {
        $kategori = Kategori::firstOrCreate(
            ['id_kategori' => 99],
            ['kategori' => 'Test Kategori']
        );

        $kategori->nama_kategori = 'Kategori Diubah';
        $kategori->save();

        $this->assertEquals('Kategori Diubah', $kategori->fresh()->getAttributes()['kategori']);
    }

    // U28 - virtual attribute: get nama_kategori → baca dari kolom kategori
    public function test_get_nama_kategori_reads_from_kategori_column()
    {
        $kategori = Kategori::firstOrCreate(
            ['id_kategori' => 98],
            ['kategori' => 'Indikator Nasional Mutu']
        );

        $this->assertEquals('Indikator Nasional Mutu', $kategori->nama_kategori);
    }

    // U29 - hasMany IndikatorMutu — instanceof HasMany
    public function test_has_many_indikator_mutu_relation()
    {
        $kategori = new Kategori();
        $relation = $kategori->indikatorMutus();

        $this->assertInstanceOf(HasMany::class, $relation);
    }

    // U30 - fillable mengandung id_kategori, nama_kategori, kategori
    public function test_fillable_contains_required_attributes()
    {
        $kategori = new Kategori();
        $fillable = $kategori->getFillable();

        $this->assertContains('id_kategori', $fillable);
        $this->assertContains('kategori', $fillable);
    }

    // U31 - timestamps = false
    public function test_timestamps_are_disabled()
    {
        $kategori = new Kategori();
        $this->assertFalse($kategori->timestamps);
    }
}