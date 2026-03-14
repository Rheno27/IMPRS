<?php

namespace Tests\Unit\Services;

use App\Models\BioPasien;
use App\Models\IndikatorMutu;
use App\Models\IndikatorRuangan;
use App\Models\Jawaban;
use App\Models\Kategori;
use App\Models\MutuRuangan;
use App\Models\Pertanyaan;
use App\Models\PilihanJawaban;
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

        Ruangan::firstOrCreate(['id_ruangan' => 'R01'], ['nama_ruangan' => 'Ruangan A']);
        Ruangan::firstOrCreate(['id_ruangan' => 'R02'], ['nama_ruangan' => 'Ruangan B']);
        Kategori::firstOrCreate(['id_kategori' => 1], ['kategori' => 'Indikator Nasional Mutu']);
    }

    // =========================================================================
    // HELPER METHODS
    // =========================================================================

    private function createIndikatorRuangan(array $overrides = []): IndikatorRuangan
    {
        IndikatorMutu::firstOrCreate(
            ['id_indikator' => 1],
            ['id_kategori' => 1, 'variabel' => 'Variabel Test', 'standar' => 90]
        );

        return IndikatorRuangan::create(array_merge([
            'id_ruangan' => 'R01',
            'id_indikator' => 1,
            'active' => true,
        ], $overrides));
    }

    private function createFreshIndikatorRuangan(
        string $idRuangan = 'R01',
        string $variabel = 'Indikator Test',
        array $overrides = []
    ): IndikatorRuangan {
        $indikator = IndikatorMutu::create([
            'id_kategori' => 1,
            'variabel' => $variabel,
            'standar' => '90',
        ]);

        return IndikatorRuangan::create(array_merge([
            'id_ruangan' => $idRuangan,
            'id_indikator' => $indikator->id_indikator,
            'active' => true,
        ], $overrides));
    }

    private function createStrukturSkm(): array
    {
        $pertanyaan = Pertanyaan::create([
            'pertanyaan' => 'Pertanyaan SKM Test ' . uniqid(),
            'urutan' => 99,
        ]);

        $pilihanBaik = PilihanJawaban::create([
            'id_pertanyaan' => $pertanyaan->id_pertanyaan,
            'pilihan' => 'Sangat Baik',
            'nilai' => 4,
        ]);

        $pilihanCukup = PilihanJawaban::create([
            'id_pertanyaan' => $pertanyaan->id_pertanyaan,
            'pilihan' => 'Cukup',
            'nilai' => 2,
        ]);

        return [$pertanyaan, $pilihanBaik, $pilihanCukup];
    }

    private function createJawabanSkm(
        string $idRuangan,
        string $noRm,
        string $tanggal,
        PilihanJawaban $pilihan
    ): Jawaban {
        $pasien = BioPasien::create([
            'id_ruangan' => $idRuangan,
            'no_rm' => $noRm,
            'umur' => 30,
            'jenis_kelamin' => 'L',
            'pendidikan' => 'SMA',
            'pekerjaan' => 'Swasta',
        ]);

        return Jawaban::create([
            'tanggal' => $tanggal,
            'id_pasien' => $pasien->id_pasien,
            'id_pertanyaan' => $pilihan->id_pertanyaan,
            'id_pilihan' => $pilihan->id_pilihan,
            'hasil_nilai' => $pilihan->nilai,
        ]);
    }

    // =========================================================================
    // calculateDailyStats()
    // =========================================================================

    // U46 - Mengembalikan persentase yang benar (8/10 = 80%)
    public function test_calculate_daily_stats_returns_correct_percentage()
    {
        $ir = $this->createIndikatorRuangan();

        MutuRuangan::create([
            'id_indikator_ruangan' => $ir->id_indikator_ruangan,
            'tanggal' => '2025-01-15',
            'pasien_sesuai' => 8,
            'total_pasien' => 10,
        ]);

        $result = $this->service->calculateDailyStats(
            $ir->id_indikator_ruangan,
            1,
            2025
        );

        $this->assertEquals(80.0, $result['2025-01-15']['persentase']);
    }

    // U47 - Mengembalikan array kosong jika tidak ada data
    public function test_calculate_daily_stats_returns_empty_when_no_data()
    {
        $ir = $this->createIndikatorRuangan();

        $result = $this->service->calculateDailyStats(
            $ir->id_indikator_ruangan,
            1,
            2025
        );

        $this->assertEmpty($result);
    }

    // U48 - Mengelompokkan data berdasarkan tanggal
    public function test_calculate_daily_stats_groups_by_tanggal()
    {
        $ir = $this->createIndikatorRuangan();

        MutuRuangan::create([
            'id_indikator_ruangan' => $ir->id_indikator_ruangan,
            'tanggal' => '2025-01-10',
            'pasien_sesuai' => 5,
            'total_pasien' => 10,
        ]);
        MutuRuangan::create([
            'id_indikator_ruangan' => $ir->id_indikator_ruangan,
            'tanggal' => '2025-01-15',
            'pasien_sesuai' => 9,
            'total_pasien' => 10,
        ]);

        $result = $this->service->calculateDailyStats(
            $ir->id_indikator_ruangan,
            1,
            2025
        );

        $this->assertArrayHasKey('2025-01-10', $result);
        $this->assertArrayHasKey('2025-01-15', $result);
        $this->assertEquals(50.0, $result['2025-01-10']['persentase']);
        $this->assertEquals(90.0, $result['2025-01-15']['persentase']);
    }

    // =========================================================================
    // simpanDataMutu()
    // =========================================================================

    // U49 - Membuat record baru di mutu_ruangan
    public function test_simpan_data_mutu_creates_new_record()
    {
        $ir = $this->createIndikatorRuangan();

        $this->service->simpanDataMutu(
            $ir->id_indikator_ruangan,
            '2025-01-15',
            8,
            10
        );

        $this->assertDatabaseHas('mutu_ruangan', [
            'id_indikator_ruangan' => $ir->id_indikator_ruangan,
            'tanggal' => '2025-01-15',
            'pasien_sesuai' => 8,
            'total_pasien' => 10,
        ]);
    }

    // U50 - Update record yang sudah ada — tidak duplikat untuk tanggal yang sama
    public function test_simpan_data_mutu_updates_existing_record_without_duplicate()
    {
        $ir = $this->createIndikatorRuangan();

        $this->service->simpanDataMutu($ir->id_indikator_ruangan, '2025-01-15', 5, 10);
        $this->service->simpanDataMutu($ir->id_indikator_ruangan, '2025-01-15', 8, 10);

        $count = MutuRuangan::where('id_indikator_ruangan', $ir->id_indikator_ruangan)
            ->where('tanggal', '2025-01-15')
            ->count();

        $this->assertEquals(1, $count);
        $this->assertDatabaseHas('mutu_ruangan', [
            'id_indikator_ruangan' => $ir->id_indikator_ruangan,
            'tanggal' => '2025-01-15',
            'pasien_sesuai' => 8,
        ]);
    }

    // =========================================================================
    // deactivateIndikator()
    // =========================================================================

    // U51 - Mengubah active menjadi false
    public function test_deactivate_indikator_sets_active_to_false()
    {
        $ir = $this->createIndikatorRuangan(['active' => true]);

        $this->service->deactivateIndikator($ir->id_indikator_ruangan);

        $this->assertDatabaseHas('indikator_ruangan', [
            'id_indikator_ruangan' => $ir->id_indikator_ruangan,
            'active' => false,
        ]);
    }

    // =========================================================================
    // assignIndikatorToRuangan()
    // =========================================================================

    // U52 - Membuat record baru dengan active = true
    public function test_assign_indikator_to_ruangan_creates_active_record()
    {
        IndikatorMutu::firstOrCreate(
            ['id_indikator' => 1],
            ['id_kategori' => 1, 'variabel' => 'Variabel Test', 'standar' => 90]
        );

        $this->service->assignIndikatorToRuangan('R01', 1);

        $this->assertDatabaseHas('indikator_ruangan', [
            'id_ruangan' => 'R01',
            'id_indikator' => 1,
            'active' => true,
        ]);
    }

    // =========================================================================
    // getSkmData()
    // =========================================================================

    // U53 - Mengembalikan format array yang benar
    public function test_get_skm_data_returns_correct_format()
    {
        $result = $this->service->getSkmData(1, 2025);

        $this->assertArrayHasKey('variabel', $result);
        $this->assertArrayHasKey('byTanggal', $result);
        $this->assertArrayHasKey('jumlah_total', $result);
        $this->assertArrayHasKey('jumlah_sesuai', $result);
        $this->assertArrayHasKey('persen', $result);
        $this->assertEquals('Kepuasan Masyarakat', $result['variabel']);
    }

    // U54 - Mengembalikan nilai default jika tidak ada data jawaban
    public function test_get_skm_data_returns_default_values_when_no_data()
    {
        $result = $this->service->getSkmData(6, 2099);

        $this->assertEquals('Kepuasan Masyarakat', $result['variabel']);
        $this->assertEquals(0, $result['persen']);
        $this->assertEquals(0, $result['jumlah_total']);
        $this->assertEquals(0, $result['jumlah_sesuai']);
        $this->assertIsArray($result['byTanggal']);
        $this->assertEmpty($result['byTanggal']);
    }

    // U55 - Menghitung persen dengan benar dari data aktual
    public function test_get_skm_data_calculates_persen_correctly()
    {
        [, $pilihanBaik] = $this->createStrukturSkm();
        $this->createJawabanSkm('R01', '10001', '2025-01-15', $pilihanBaik);

        $result = $this->service->getSkmData(1, 2025);

        $this->assertEquals(4, $result['jumlah_sesuai']);
        $this->assertEquals(4, $result['jumlah_total']);
        $this->assertEquals(100.0, $result['persen']);
    }

    // U56 - Mengisi byTanggal dengan key berupa hari (format 'j')
    public function test_get_skm_data_groups_by_tanggal_with_day_key()
    {
        [, $pilihanBaik] = $this->createStrukturSkm();

        $this->createJawabanSkm('R01', '10002', '2025-01-15', $pilihanBaik);
        $this->createJawabanSkm('R01', '10003', '2025-01-20', $pilihanBaik);

        $result = $this->service->getSkmData(1, 2025);

        $this->assertArrayHasKey(15, $result['byTanggal']);
        $this->assertArrayHasKey(20, $result['byTanggal']);
    }

    // U57 - Setiap entry byTanggal punya property pasien_sesuai dan total_pasien
    public function test_get_skm_data_by_tanggal_entries_have_correct_properties()
    {
        [, $pilihanBaik] = $this->createStrukturSkm();
        $this->createJawabanSkm('R01', '10004', '2025-02-10', $pilihanBaik);

        $result = $this->service->getSkmData(2, 2025);

        $this->assertNotEmpty($result['byTanggal']);
        $entry = $result['byTanggal'][10];
        $this->assertIsObject($entry);
        $this->assertObjectHasProperty('pasien_sesuai', $entry);
        $this->assertObjectHasProperty('total_pasien', $entry);
    }

    // U58 - Mengagregasi multiple jawaban pada hari yang sama
    public function test_get_skm_data_aggregates_multiple_answers_on_same_day()
    {
        [, $pilihanBaik, $pilihanCukup] = $this->createStrukturSkm();

        $this->createJawabanSkm('R01', '10005', '2025-03-05', $pilihanBaik);
        $this->createJawabanSkm('R01', '10006', '2025-03-05', $pilihanCukup);

        $result = $this->service->getSkmData(3, 2025);

        $this->assertEquals(6, $result['jumlah_sesuai']);
        $this->assertEquals(8, $result['jumlah_total']);
        $this->assertEquals(75.0, $result['persen']);
        $this->assertCount(1, $result['byTanggal']);
        $this->assertArrayHasKey(5, $result['byTanggal']);
    }

    // =========================================================================
    // buildChartSeriesForRuangan()
    // =========================================================================

    // U59 - Mengembalikan array kosong jika tidak ada data mutu
    public function test_build_chart_series_returns_empty_when_no_data()
    {
        $result = $this->service->buildChartSeriesForRuangan('R01', 2025);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    // U60 - Mengembalikan 12 slot monthly per indikator
    public function test_build_chart_series_returns_12_monthly_slots_per_indikator()
    {
        $ir = $this->createIndikatorRuangan();

        MutuRuangan::create([
            'id_indikator_ruangan' => $ir->id_indikator_ruangan,
            'tanggal' => '2025-03-10',
            'pasien_sesuai' => 8,
            'total_pasien' => 10,
        ]);

        $result = $this->service->buildChartSeriesForRuangan('R01', 2025);

        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('label', $result[0]);
        $this->assertArrayHasKey('monthly', $result[0]);
        $this->assertCount(12, $result[0]['monthly']);
        $this->assertEquals(80.0, $result[0]['monthly'][2]); // Maret = index 2
    }

    // U61 - Mengisi slot bulan yang ada dengan persentase benar
    public function test_build_chart_series_fills_correct_month_slot_with_percentage()
    {
        $ir = $this->createFreshIndikatorRuangan('R01', 'Indikator APD');

        MutuRuangan::create([
            'id_indikator_ruangan' => $ir->id_indikator_ruangan,
            'tanggal' => '2025-03-15',
            'pasien_sesuai' => 9,
            'total_pasien' => 10,
        ]);

        $result = $this->service->buildChartSeriesForRuangan('R01', 2025);

        $this->assertEquals(90.0, $result[0]['monthly'][2]);
        $this->assertNull($result[0]['monthly'][0]);  // Januari kosong
        $this->assertNull($result[0]['monthly'][11]); // Desember kosong
    }

    // U62 - Mengembalikan key label, monthly, dan kategori
    public function test_build_chart_series_entries_have_required_keys()
    {
        $ir = $this->createFreshIndikatorRuangan('R01', 'Indikator Keselamatan Pasien');

        MutuRuangan::create([
            'id_indikator_ruangan' => $ir->id_indikator_ruangan,
            'tanggal' => '2025-05-20',
            'pasien_sesuai' => 5,
            'total_pasien' => 10,
        ]);

        $result = $this->service->buildChartSeriesForRuangan('R01', 2025);

        $this->assertArrayHasKey('label', $result[0]);
        $this->assertArrayHasKey('monthly', $result[0]);
        $this->assertArrayHasKey('kategori', $result[0]);
        $this->assertEquals('Indikator Keselamatan Pasien', $result[0]['label']);
    }

    // U63 - Mengagregasi beberapa hari dalam bulan yang sama
    public function test_build_chart_series_aggregates_multiple_days_in_same_month()
    {
        $ir = $this->createFreshIndikatorRuangan('R01', 'Indikator Multi Hari');

        // Februari: 8/10 + 6/10 = 14/20 = 70%
        MutuRuangan::create([
            'id_indikator_ruangan' => $ir->id_indikator_ruangan,
            'tanggal' => '2025-02-05',
            'pasien_sesuai' => 8,
            'total_pasien' => 10,
        ]);
        MutuRuangan::create([
            'id_indikator_ruangan' => $ir->id_indikator_ruangan,
            'tanggal' => '2025-02-20',
            'pasien_sesuai' => 6,
            'total_pasien' => 10,
        ]);

        $result = $this->service->buildChartSeriesForRuangan('R01', 2025);

        $this->assertEquals(70.0, $result[0]['monthly'][1]); // Februari = index 1
    }

    // U64 - Hanya mengembalikan data ruangan yang diminta
    public function test_build_chart_series_isolates_data_by_ruangan()
    {
        $irR01 = $this->createFreshIndikatorRuangan('R01', 'Indikator R01 Isolasi');
        MutuRuangan::create([
            'id_indikator_ruangan' => $irR01->id_indikator_ruangan,
            'tanggal' => '2025-04-10',
            'pasien_sesuai' => 8,
            'total_pasien' => 10,
        ]);

        $irR02 = $this->createFreshIndikatorRuangan('R02', 'Indikator R02 Isolasi');
        MutuRuangan::create([
            'id_indikator_ruangan' => $irR02->id_indikator_ruangan,
            'tanggal' => '2025-04-10',
            'pasien_sesuai' => 5,
            'total_pasien' => 10,
        ]);

        $result = $this->service->buildChartSeriesForRuangan('R01', 2025);

        $this->assertCount(1, $result);
        $this->assertEquals('Indikator R01 Isolasi', $result[0]['label']);
    }

    // =========================================================================
    // switchIndikatorRuangan()
    // =========================================================================

    // U65 - Menonaktifkan indikator lama (active = false)
    public function test_switch_indikator_sets_old_indikator_to_inactive()
    {
        $irLama = $this->createFreshIndikatorRuangan('R01', 'Indikator Lama Switch');
        $indikatorBaru = IndikatorMutu::create([
            'id_kategori' => 1,
            'variabel' => 'Indikator Baru Switch',
            'standar' => '85',
        ]);

        $this->service->switchIndikatorRuangan(
            'R01',
            $irLama->id_indikator_ruangan,
            $indikatorBaru->id_indikator
        );

        $this->assertDatabaseHas('indikator_ruangan', [
            'id_indikator_ruangan' => $irLama->id_indikator_ruangan,
            'active' => false,
        ]);
    }

    // U66 - Mengaktifkan indikator baru (active = true)
    public function test_switch_indikator_sets_new_indikator_to_active()
    {
        $irLama = $this->createFreshIndikatorRuangan('R01', 'Indikator Lama Aktif');
        $indikatorBaru = IndikatorMutu::create([
            'id_kategori' => 1,
            'variabel' => 'Indikator Baru Aktif',
            'standar' => '85',
        ]);

        $this->service->switchIndikatorRuangan(
            'R01',
            $irLama->id_indikator_ruangan,
            $indikatorBaru->id_indikator
        );

        $this->assertDatabaseHas('indikator_ruangan', [
            'id_ruangan' => 'R01',
            'id_indikator' => $indikatorBaru->id_indikator,
            'active' => true,
        ]);
    }

    // U67 - Berjalan dalam satu transaksi dan return true
    public function test_switch_indikator_is_transactional_and_returns_true()
    {
        $irLama = $this->createFreshIndikatorRuangan('R01', 'Indikator Transaksi Lama');
        $indikatorBaru = IndikatorMutu::create([
            'id_kategori' => 1,
            'variabel' => 'Indikator Transaksi Baru',
            'standar' => '85',
        ]);

        $result = $this->service->switchIndikatorRuangan(
            'R01',
            $irLama->id_indikator_ruangan,
            $indikatorBaru->id_indikator
        );

        $this->assertTrue($result);
        $this->assertDatabaseHas('indikator_ruangan', [
            'id_indikator_ruangan' => $irLama->id_indikator_ruangan,
            'active' => false,
        ]);
        $this->assertDatabaseHas('indikator_ruangan', [
            'id_ruangan' => 'R01',
            'id_indikator' => $indikatorBaru->id_indikator,
            'active' => true,
        ]);
    }

    // U68 - Mengaktifkan kembali record nonaktif yang sudah ada (tidak duplikat)
    public function test_switch_indikator_reactivates_existing_inactive_record()
    {
        $irLama = $this->createFreshIndikatorRuangan('R01', 'Indikator Reaktivasi Lama');
        $indikatorBaru = IndikatorMutu::create([
            'id_kategori' => 1,
            'variabel' => 'Indikator Reaktivasi Baru',
            'standar' => '85',
        ]);
        $irBaruNonaktif = IndikatorRuangan::create([
            'id_ruangan' => 'R01',
            'id_indikator' => $indikatorBaru->id_indikator,
            'active' => false,
        ]);

        $this->service->switchIndikatorRuangan(
            'R01',
            $irLama->id_indikator_ruangan,
            $indikatorBaru->id_indikator
        );

        // Record lama di-reaktivasi, bukan buat baru
        $this->assertDatabaseHas('indikator_ruangan', [
            'id_indikator_ruangan' => $irBaruNonaktif->id_indikator_ruangan,
            'active' => true,
        ]);

        // Tidak boleh ada duplikat record aktif
        $count = IndikatorRuangan::where('id_ruangan', 'R01')
            ->where('id_indikator', $indikatorBaru->id_indikator)
            ->where('active', true)
            ->count();

        $this->assertEquals(1, $count);
    }
}