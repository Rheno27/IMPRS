<?php

namespace Tests\Unit\Models;

use App\Models\IndikatorMutu;
use App\Models\IndikatorRuangan;
use App\Models\Kategori;
use App\Models\MutuRuangan;
use App\Models\Ruangan;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MutuRuanganTest extends TestCase
{
    use DatabaseTransactions;

    // U18 - method indikatorRuangan() exists
    public function test_indikator_ruangan_method_exists()
    {
        $mutu = new MutuRuangan();
        $this->assertTrue(method_exists($mutu, 'indikatorRuangan'));
    }

    // U19 - belongsTo IndikatorRuangan — instanceof BelongsTo
    public function test_belongs_to_indikator_ruangan_relation()
    {
        $mutu = new MutuRuangan();
        $relation = $mutu->indikatorRuangan();

        $this->assertInstanceOf(BelongsTo::class, $relation);
    }

    // U20 - fillable mengandung tanggal, id_indikator_ruangan, total_pasien, pasien_sesuai
    public function test_fillable_contains_required_attributes()
    {
        $mutu = new MutuRuangan();
        $fillable = $mutu->getFillable();

        $this->assertContains('tanggal', $fillable);
        $this->assertContains('id_indikator_ruangan', $fillable);
        $this->assertContains('total_pasien', $fillable);
        $this->assertContains('pasien_sesuai', $fillable);
    }

    // U21 - timestamps = false dan incrementing = true
    public function test_timestamps_disabled_and_incrementing_true()
    {
        $mutu = new MutuRuangan();
        $this->assertFalse($mutu->timestamps);
        $this->assertTrue($mutu->incrementing);
    }

    // U21b - relasi ke IndikatorRuangan terbaca dari DB
    public function test_relation_to_indikator_ruangan_loads_from_database()
    {
        Ruangan::firstOrCreate(['id_ruangan' => 'R01'], ['nama_ruangan' => 'Ruangan A']);
        Kategori::firstOrCreate(['id_kategori' => 1], ['kategori' => 'Indikator Nasional Mutu']);

        $indikator = IndikatorMutu::create([
            'id_kategori' => 1,
            'variabel' => 'Variabel Test MutuRuangan',
            'standar' => '90',
        ]);

        $ir = IndikatorRuangan::create([
            'id_ruangan' => 'R01',
            'id_indikator' => $indikator->id_indikator,
            'active' => true,
        ]);

        $mutu = MutuRuangan::create([
            'id_indikator_ruangan' => $ir->id_indikator_ruangan,
            'tanggal' => '2025-01-15',
            'pasien_sesuai' => 8,
            'total_pasien' => 10,
        ]);

        $this->assertInstanceOf(IndikatorRuangan::class, $mutu->indikatorRuangan);
        $this->assertEquals($ir->id_indikator_ruangan, $mutu->indikatorRuangan->id_indikator_ruangan);
    }
}