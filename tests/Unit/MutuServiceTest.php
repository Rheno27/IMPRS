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
        Ruangan::firstOrCreate(['id_ruangan' => 'R01'], ['nama_ruangan' => 'Ruangan A']);
        IndikatorMutu::firstOrCreate(['id_indikator' => 1], [
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

    // U43 - getSkmData() mengembalikan format yang benar (keys + default jika tidak ada data)
    public function test_get_skm_data_returns_correct_format()
    {
        $result = $this->service->getSkmData(1, 2025);

        $this->assertArrayHasKey('variabel', $result);
        $this->assertArrayHasKey('byTanggal', $result);
        $this->assertArrayHasKey('jumlah_total', $result);
        $this->assertArrayHasKey('jumlah_sesuai', $result);
        $this->assertArrayHasKey('persen', $result);
        $this->assertEquals('Kepuasan Masyarakat', $result['variabel']);
        $this->assertEquals(0, $result['persen']);
    }

    // U44 - buildChartSeriesForRuangan() mengembalikan array kosong jika tidak ada data mutu
    public function test_build_chart_series_returns_empty_when_no_data()
    {
        Ruangan::firstOrCreate(['id_ruangan' => 'R01'], ['nama_ruangan' => 'Ruangan A']);

        $result = $this->service->buildChartSeriesForRuangan('R01', 2025);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    // U45 - buildChartSeriesForRuangan() mengembalikan data 12 bulan per indikator
    public function test_build_chart_series_returns_12_monthly_slots_per_indikator()
    {
        $ir = $this->createIndikatorRuangan();

        \App\Models\MutuRuangan::create([
            'id_indikator_ruangan' => $ir->id_indikator_ruangan,
            'tanggal' => '2025-03-10',
            'pasien_sesuai' => 8,
            'total_pasien' => 10,
        ]);

        $result = $this->service->buildChartSeriesForRuangan('R01', 2025);

        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('label', $result[0]);
        $this->assertArrayHasKey('monthly', $result[0]);
        // Harus ada 12 slot (satu per bulan)
        $this->assertCount(12, $result[0]['monthly']);
        // Bulan Maret (index 2) harus terisi = 80%
        $this->assertEquals(80.0, $result[0]['monthly'][2]);
    }
}