<?php

namespace Tests\Unit\Models;

use App\Models\BioPasien;
use App\Models\Ruangan;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BioPasienTest extends TestCase
{
    use DatabaseTransactions;

    // U43 - fillable mengandung id_ruangan, no_rm, umur, jenis_kelamin, pendidikan, pekerjaan
    public function test_fillable_contains_required_attributes()
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

    // U44 - timestamps = false
    public function test_timestamps_are_disabled()
    {
        $bioPasien = new BioPasien();
        $this->assertFalse($bioPasien->timestamps);
    }

    // U45 - primaryKey = 'id_pasien'
    public function test_primary_key_is_id_pasien()
    {
        $bioPasien = new BioPasien();
        $this->assertEquals('id_pasien', $bioPasien->getKeyName());
    }

    // U45b - record tersimpan dan terbaca dengan PK yang benar
    public function test_record_stored_and_retrieved_with_correct_primary_key()
    {
        Ruangan::firstOrCreate(['id_ruangan' => 'R01'], ['nama_ruangan' => 'Ruangan A']);

        $pasien = BioPasien::create([
            'id_ruangan' => 'R01',
            'no_rm' => '99001',
            'umur' => 25,
            'jenis_kelamin' => 'P',
            'pendidikan' => 'D3',
            'pekerjaan' => 'Swasta',
        ]);

        $this->assertNotNull($pasien->id_pasien);
        $this->assertEquals('99001', $pasien->no_rm);

        $retrieved = BioPasien::find($pasien->id_pasien);
        $this->assertNotNull($retrieved);
        $this->assertEquals($pasien->id_pasien, $retrieved->id_pasien);
    }
}