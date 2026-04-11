<?php

namespace Tests\Unit\Models;

use App\Models\IndikatorMutu;
use App\Models\IndikatorRuangan;
use App\Models\Kategori;
use App\Models\Ruangan;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class IndikatorRuanganTest extends TestCase
{
    use DatabaseTransactions;

    // U12 - active flag bisa di-set false
    public function test_active_flag_can_be_set_to_false()
    {
        $ir = new IndikatorRuangan(['active' => false]);
        $this->assertFalse((bool) $ir->active);
    }

    // U13 - belongsTo IndikatorMutu — instanceof BelongsTo
    public function test_belongs_to_indikator_mutu_relation()
    {
        $ir = new IndikatorRuangan();
        $relation = $ir->indikatorMutu();

        $this->assertInstanceOf(BelongsTo::class, $relation);
    }

    // U14 - belongsTo Ruangan — instanceof BelongsTo
    public function test_belongs_to_ruangan_relation()
    {
        $ir = new IndikatorRuangan();
        $relation = $ir->ruangan();

        $this->assertInstanceOf(BelongsTo::class, $relation);
    }

    // U15 - hasMany MutuRuangan — instanceof HasMany
    public function test_has_many_mutu_ruangan_relation()
    {
        $ir = new IndikatorRuangan();
        $relation = $ir->mutuRuangan();

        $this->assertInstanceOf(HasMany::class, $relation);
    }

    // U16 - fillable mengandung id_ruangan, id_indikator, active
    public function test_fillable_contains_required_attributes()
    {
        $ir = new IndikatorRuangan();
        $fillable = $ir->getFillable();

        $this->assertContains('id_ruangan', $fillable);
        $this->assertContains('id_indikator', $fillable);
        $this->assertContains('active', $fillable);
    }

    // U17 - timestamps = false
    public function test_timestamps_are_disabled()
    {
        $ir = new IndikatorRuangan();
        $this->assertFalse($ir->timestamps);
    }

    // U17b - relasi terbaca dengan benar dari DB
    public function test_relations_load_correctly_from_database()
    {
        Ruangan::firstOrCreate(['id_ruangan' => 'R01'], ['nama_ruangan' => 'Ruangan A']);
        Kategori::firstOrCreate(['id_kategori' => 1], ['kategori' => 'Indikator Nasional Mutu']);

        $indikator = IndikatorMutu::create([
            'id_kategori' => 1,
            'variabel' => 'Variabel Test Relasi',
            'standar' => '90',
        ]);

        $ir = IndikatorRuangan::create([
            'id_ruangan' => 'R01',
            'id_indikator' => $indikator->id_indikator,
            'active' => true,
        ]);

        $this->assertInstanceOf(IndikatorMutu::class, $ir->indikatorMutu);
        $this->assertInstanceOf(Ruangan::class, $ir->ruangan);
        $this->assertEquals('R01', $ir->ruangan->id_ruangan);
        $this->assertEquals('Variabel Test Relasi', $ir->indikatorMutu->variabel);
    }
}