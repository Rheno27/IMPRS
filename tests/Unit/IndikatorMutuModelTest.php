<?php

namespace Tests\Unit;

use App\Models\IndikatorMutu;
use App\Models\IndikatorRuangan;
use App\Models\Kategori;
use App\Models\MutuRuangan;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class IndikatorMutuModelTest extends TestCase
{
    use DatabaseTransactions;

    // U06 - IndikatorMutu belongsTo Kategori
    public function test_indikator_mutu_belongs_to_kategori()
    {
        $kategori = Kategori::firstOrCreate([
            'kategori' => 'Test Kategori A'
        ]);

        $indikator = IndikatorMutu::create([
            'id_kategori' => $kategori->id_kategori,
            'variabel' => 'Variabel Test Unique',
            'standar' => 90,
        ]);

        $this->assertInstanceOf(Kategori::class, $indikator->kategori);
        $this->assertEquals('Test Kategori A', $indikator->kategori->nama_kategori);
    }

    // U07 - IndikatorMutu hasMany IndikatorRuangan
    public function test_indikator_mutu_has_many_indikator_ruangan()
    {
        $kategori = Kategori::firstOrCreate(['id_kategori' => 1], ['kategori' => 'Kategori A']);
        $indikator = IndikatorMutu::create([
            'id_kategori' => $kategori->id_kategori,
            'variabel' => 'Variabel Test Unique',
            'standar' => 90,
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $indikator->indikatorRuangan);
    }

    // U08 - Fillable attributes are correctly defined
    public function test_indikator_mutu_fillable_attributes()
    {
        $model = new IndikatorMutu();
        $fillable = $model->getFillable();

        $this->assertContains('id_indikator', $fillable);
        $this->assertContains('variabel', $fillable);
        $this->assertContains('standar', $fillable);
    }

    // U09 - Timestamps are disabled
    public function test_indikator_mutu_timestamps_disabled()
    {
        $model = new IndikatorMutu();
        $this->assertFalse($model->timestamps);
    }

    // U10 - IndikatorRuangan active flag can be set to false
    public function test_indikator_ruangan_active_flag_can_be_false()
    {
        $indikatorRuangan = new IndikatorRuangan([
            'active' => false,
        ]);

        $this->assertFalse((bool) $indikatorRuangan->active);
    }

    // U11 - MutuRuangan belongsTo IndikatorRuangan
    public function test_mutu_ruangan_belongs_to_indikator_ruangan()
    {
        $mutu = new MutuRuangan();
        $this->assertTrue(method_exists($mutu, 'indikatorRuangan'));
    }
}
