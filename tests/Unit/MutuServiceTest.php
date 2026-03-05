<?php

namespace Tests\Unit;

use App\Models\IndikatorMutu;
use App\Models\IndikatorRuangan;
use App\Models\MutuRuangan;
use App\Models\Ruangan;
use App\Services\MutuService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MutuServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected MutuService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MutuService();
    }

    // U18 - calculateDailyStats() returns correct percentage
    public function test_calculate_daily_stats_returns_correct_percentage()
    {
        $indikatorRuangan = $this->createIndikatorRuangan();

        MutuRuangan::create([
            'id_indikator_ruangan' => $indikatorRuangan->id_indikator_ruangan,
            'tanggal' => '2025-01-15',
            'pasien_sesuai' => 8,
            'total_pasien' => 10,
        ]);

        $result = $this->service->calculateDailyStats($indikatorRuangan->id_indikator_ruangan, 1, 2025);

        $this->assertEquals(80.0, $result['2025-01-15']['persentase']);
    }

    // U19 - calculateDailyStats() returns empty/zero when no data
    public function test_calculate_daily_stats_returns_empty_when_no_data()
    {
        $indikatorRuangan = $this->createIndikatorRuangan();

        $result = $this->service->calculateDailyStats($indikatorRuangan->id_indikator_ruangan, 1, 2025);

        $this->assertEmpty($result);
    }

    // U20 - calculateDailyStats() groups by tanggal correctly
    public function test_calculate_daily_stats_groups_by_tanggal()
    {
        $indikatorRuangan = $this->createIndikatorRuangan();

        MutuRuangan::create([
            'id_indikator_ruangan' => $indikatorRuangan->id_indikator_ruangan,
            'tanggal' => '2025-01-10',
            'pasien_sesuai' => 5,
            'total_pasien' => 10,
        ]);
        MutuRuangan::create([
            'id_indikator_ruangan' => $indikatorRuangan->id_indikator_ruangan,
            'tanggal' => '2025-01-15',
            'pasien_sesuai' => 9,
            'total_pasien' => 10,
        ]);

        $result = $this->service->calculateDailyStats($indikatorRuangan->id_indikator_ruangan, 1, 2025);

        $this->assertArrayHasKey('2025-01-10', $result);
        $this->assertArrayHasKey('2025-01-15', $result);
        $this->assertEquals(50.0, $result['2025-01-10']['persentase']);
        $this->assertEquals(90.0, $result['2025-01-15']['persentase']);
    }

    // U21 - simpanDataMutu() creates new record
    public function test_simpan_data_mutu_creates_new_record()
    {
        $indikatorRuangan = $this->createIndikatorRuangan();

        $this->service->simpanDataMutu(
            $indikatorRuangan->id_indikator_ruangan,
            '2025-01-15',
            8,
            10
        );

        $this->assertDatabaseHas('mutu_ruangan', [
            'id_indikator_ruangan' => $indikatorRuangan->id_indikator_ruangan,
            'tanggal' => '2025-01-15',
            'pasien_sesuai' => 8,
            'total_pasien' => 10,
        ]);
    }

    // U22 - simpanDataMutu() updates existing record (no duplicate for same date)
    public function test_simpan_data_mutu_updates_existing_record()
    {
        $indikatorRuangan = $this->createIndikatorRuangan();

        $this->service->simpanDataMutu($indikatorRuangan->id_indikator_ruangan, '2025-01-15', 5, 10);
        $this->service->simpanDataMutu($indikatorRuangan->id_indikator_ruangan, '2025-01-15', 8, 10);

        $count = MutuRuangan::where('id_indikator_ruangan', $indikatorRuangan->id_indikator_ruangan)
            ->where('tanggal', '2025-01-15')
            ->count();

        $this->assertEquals(1, $count);

        $this->assertDatabaseHas('mutu_ruangan', [
            'id_indikator_ruangan' => $indikatorRuangan->id_indikator_ruangan,
            'tanggal' => '2025-01-15',
            'pasien_sesuai' => 8,
        ]);
    }

    // U23 - deactivateIndikator() sets active to false
    public function test_deactivate_indikator_sets_active_to_false()
    {
        $indikatorRuangan = $this->createIndikatorRuangan(['active' => true]);

        $this->service->deactivateIndikator($indikatorRuangan->id_indikator_ruangan);

        $this->assertDatabaseHas('indikator_ruangan', [
            'id_indikator_ruangan' => $indikatorRuangan->id_indikator_ruangan,
            'active' => false,
        ]);
    }

    // U24 - assignIndikatorToRuangan() creates new record with active = true
    public function test_assign_indikator_to_ruangan_creates_active_record()
    {
        $ruangan = Ruangan::firstOrCreate(['id_ruangan' => 'R01'], ['nama_ruangan' => 'Ruangan A']);
        $indikator = IndikatorMutu::firstOrCreate([
            'id_indikator' => 1,
        ], [
            'variabel' => 'Variabel Test',
            'standar' => 90,
        ]);

        $this->service->assignIndikatorToRuangan('R01', 1);

        $this->assertDatabaseHas('indikator_ruangan', [
            'id_ruangan' => 'R01',
            'id_indikator' => 1,
            'active' => true,
        ]);
    }

    // Helper method
    private function createIndikatorRuangan(array $overrides = []): IndikatorRuangan
    {
        Ruangan::firstOrCreate(['id_ruangan' => 'R01'], ['nama_ruangan' => 'Ruangan A']);
        IndikatorMutu::firstOrCreate(['id_indikator' => 1], ['variabel' => 'Variabel Test', 'standar' => 90]);

        return IndikatorRuangan::create(array_merge([
            'id_indikator_ruangan' => 1,
            'id_ruangan' => 'R01',
            'id_indikator' => 1,
            'active' => true,
        ], $overrides));
    }
}
