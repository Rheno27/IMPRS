<?php

namespace Tests\Unit\Models;

use App\Models\BioPasien;
use App\Models\Jawaban;
use App\Models\Pertanyaan;
use App\Models\PilihanJawaban;
use App\Models\Ruangan;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class JawabanTest extends TestCase
{
    use DatabaseTransactions;

    // U38 - belongsTo PilihanJawaban via pilihanJawaban() — instanceof BelongsTo
    public function test_belongs_to_pilihan_jawaban_relation()
    {
        $jawaban = new Jawaban();
        $relation = $jawaban->pilihanJawaban();

        $this->assertInstanceOf(BelongsTo::class, $relation);
    }

    // U39 - belongsTo Pertanyaan — instanceof BelongsTo
    public function test_belongs_to_pertanyaan_relation()
    {
        $jawaban = new Jawaban();
        $relation = $jawaban->pertanyaan();

        $this->assertInstanceOf(BelongsTo::class, $relation);
    }

    // U40 - belongsTo BioPasien via pasien() — instanceof BelongsTo
    public function test_belongs_to_bio_pasien_via_pasien_relation()
    {
        $jawaban = new Jawaban();
        $relation = $jawaban->pasien();

        $this->assertInstanceOf(BelongsTo::class, $relation);
    }

    // U41 - fillable mengandung tanggal, id_pasien, id_pertanyaan, id_pilihan, hasil_nilai
    public function test_fillable_contains_required_attributes()
    {
        $jawaban = new Jawaban();
        $fillable = $jawaban->getFillable();

        $this->assertContains('tanggal', $fillable);
        $this->assertContains('id_pasien', $fillable);
        $this->assertContains('id_pertanyaan', $fillable);
        $this->assertContains('id_pilihan', $fillable);
        $this->assertContains('hasil_nilai', $fillable);
    }

    // U42 - timestamps = false
    public function test_timestamps_are_disabled()
    {
        $jawaban = new Jawaban();
        $this->assertFalse($jawaban->timestamps);
    }

    // U42b - semua 3 relasi terbaca dari DB
    public function test_all_three_relations_load_correctly_from_database()
    {
        Ruangan::firstOrCreate(['id_ruangan' => 'R01'], ['nama_ruangan' => 'Ruangan A']);

        Pertanyaan::firstOrCreate(
            ['id_pertanyaan' => 1],
            ['pertanyaan' => 'Bagaimana pelayanan?', 'urutan' => 1]
        );

        $pilihan = PilihanJawaban::firstOrCreate(
            ['id_pilihan' => 1],
            ['id_pertanyaan' => 1, 'pilihan' => 'Sangat Baik', 'nilai' => 4]
        );

        $pasien = BioPasien::create([
            'id_ruangan' => 'R01',
            'no_rm' => '12300',
            'umur' => 30,
            'jenis_kelamin' => 'L',
            'pendidikan' => 'SMA',
            'pekerjaan' => 'Swasta',
        ]);

        $jawaban = Jawaban::create([
            'tanggal' => '2025-01-15',
            'id_pasien' => $pasien->id_pasien,
            'id_pertanyaan' => 1,
            'id_pilihan' => 1,
            'hasil_nilai' => 4,
        ]);

        $this->assertInstanceOf(PilihanJawaban::class, $jawaban->pilihanJawaban);
        $this->assertInstanceOf(Pertanyaan::class, $jawaban->pertanyaan);
        $this->assertInstanceOf(BioPasien::class, $jawaban->pasien);
    }
}