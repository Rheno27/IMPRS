<?php

namespace Tests\Unit\Models;

use App\Models\IndikatorRuangan;
use App\Models\Ruangan;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RuanganTest extends TestCase
{
    use DatabaseTransactions;

    // U22 - string PK — id_ruangan='R99' tersimpan dan terbaca sebagai string
    public function test_primary_key_stored_and_read_as_string()
    {
        $ruangan = Ruangan::firstOrCreate(
            ['id_ruangan' => 'R99'],
            ['nama_ruangan' => 'Ruangan Test']
        );

        $this->assertEquals('R99', $ruangan->id_ruangan);
        $this->assertIsString($ruangan->id_ruangan);
    }

    // U23 - incrementing = false dan keyType = 'string'
    public function test_primary_key_is_non_incrementing_string()
    {
        $ruangan = new Ruangan();
        $this->assertFalse($ruangan->incrementing);
        $this->assertEquals('string', $ruangan->getKeyType());
    }

    // U24 - fillable mengandung id_ruangan, nama_ruangan
    public function test_fillable_contains_required_attributes()
    {
        $ruangan = new Ruangan();
        $fillable = $ruangan->getFillable();

        $this->assertContains('id_ruangan', $fillable);
        $this->assertContains('nama_ruangan', $fillable);
    }

    // U25 - timestamps = false
    public function test_timestamps_are_disabled()
    {
        $ruangan = new Ruangan();
        $this->assertFalse($ruangan->timestamps);
    }

    // U26 - hasMany IndikatorRuangan — instanceof HasMany
    public function test_has_many_indikator_ruangan_relation()
    {
        $ruangan = new Ruangan();
        $relation = $ruangan->indikatorRuangan();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertEquals('id_ruangan', $relation->getForeignKeyName());
    }
}