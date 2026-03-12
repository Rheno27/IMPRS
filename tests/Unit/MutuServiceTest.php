<?php

namespace Tests\Unit;

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

        // FK dasar yang dibutuhkan semua test
        Ruangan::firstOrCreate(['id_ruangan' => 'R01'], ['nama_ruangan' => 'Ruangan A']);
        Ruangan::firstOrCreate(['id_ruangan' => 'R02'], ['nama_ruangan' => 'Ruangan B']);
        Kategori::firstOrCreate(['id_kategori' => 1], ['kategori' => 'Indikator Nasional Mutu']);
    }

    // =========================================================================
    // HELPER
    // =========================================================================

    /**
     * Buat IndikatorMutu + IndikatorRuangan, return IndikatorRuangan.
     */
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

    /**
     * Buat IndikatorMutu baru dengan variabel unik + IndikatorRuangan-nya.
     * Dipakai test yang butuh indikator berbeda-beda agar tidak konflik ID.
     */
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

    /**
     * Buat Pertanyaan baru (auto-increment ID) + dua PilihanJawaban (nilai 4 dan 2).
     * Tidak pakai firstOrCreate dengan ID hardcode agar tidak terpengaruh data test lain.
     * Return: [$pertanyaan, $pilihanBaik (nilai=4), $pilihanCukup (nilai=2)]
     */
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

    /**
     * Buat BioPasien + Jawaban SKM.
     * Menerima objek PilihanJawaban langsung agar nilai yang digunakan pasti benar.
     */
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

    // U18 - calculateDailyStats() mengembalikan persentase yang benar
    public function test_calculate_daily_stats_returns_correct_percentage()
    {
        $indikatorRuangan = $this->createIndikatorRuangan();

        MutuRuangan::create([
            'id_indikator_ruangan' => $indikatorRuangan->id_indikator_ruangan,
            'tanggal' => '2025-01-15',
            'pasien_sesuai' => 8,
            'total_pasien' => 10,
        ]);

        $result = $this->service->calculateDailyStats(
            $indikatorRuangan->id_indikator_ruangan,
            1,
            2025
        );

        $this->assertEquals(80.0, $result['2025-01-15']['persentase']);
    }

    // U19 - calculateDailyStats() mengembalikan array kosong jika tidak ada data
    public function test_calculate_daily_stats_returns_empty_when_no_data()
    {
        $indikatorRuangan = $this->createIndikatorRuangan();

        $result = $this->service->calculateDailyStats(
            $indikatorRuangan->id_indikator_ruangan,
            1,
            2025
        );

        $this->assertEmpty($result);
    }

    // U20 - calculateDailyStats() mengelompokkan data berdasarkan tanggal
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

        $result = $this->service->calculateDailyStats(
            $indikatorRuangan->id_indikator_ruangan,
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

    // U21 - simpanDataMutu() membuat record baru
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

    // U22 - simpanDataMutu() mengupdate record yang sudah ada (tidak duplikat untuk tanggal sama)
    public function test_simpan_data_mutu_updates_existing_record()
    {
        $indikatorRuangan = $this->createIndikatorRuangan();

        $this->service->simpanDataMutu(
            $indikatorRuangan->id_indikator_ruangan,
            '2025-01-15',
            5,
            10
        );
        $this->service->simpanDataMutu(
            $indikatorRuangan->id_indikator_ruangan,
            '2025-01-15',
            8,
            10
        );

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

    // =========================================================================
    // deactivateIndikator()
    // =========================================================================

    // U23 - deactivateIndikator() mengubah active menjadi false
    public function test_deactivate_indikator_sets_active_to_false()
    {
        $indikatorRuangan = $this->createIndikatorRuangan(['active' => true]);

        $this->service->deactivateIndikator($indikatorRuangan->id_indikator_ruangan);

        $this->assertDatabaseHas('indikator_ruangan', [
            'id_indikator_ruangan' => $indikatorRuangan->id_indikator_ruangan,
            'active' => false,
        ]);
    }

    // =========================================================================
    // assignIndikatorToRuangan()
    // =========================================================================

    // U24 - assignIndikatorToRuangan() membuat record baru dengan active = true
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

    // U43 - getSkmData() mengembalikan format array yang benar (sudah ada sebelumnya)
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

    // U46 - getSkmData() mengembalikan nilai default jika tidak ada data jawaban
    public function test_get_skm_data_returns_default_values_when_no_data()
    {
        // Pakai tahun jauh ke depan agar pasti tidak ada data
        $result = $this->service->getSkmData(6, 2099);

        $this->assertEquals('Kepuasan Masyarakat', $result['variabel']);
        $this->assertEquals(0, $result['persen']);
        $this->assertEquals(0, $result['jumlah_total']);
        $this->assertEquals(0, $result['jumlah_sesuai']);
        $this->assertIsArray($result['byTanggal']);
        $this->assertEmpty($result['byTanggal']);
    }

    // U47 - getSkmData() menghitung persen dengan benar dari data aktual
    public function test_get_skm_data_calculates_persen_correctly()
    {
        // createStrukturSkm() pakai auto-increment ID agar tidak bentrok data test lain
        [, $pilihanBaik] = $this->createStrukturSkm();

        // Pasien memilih $pilihanBaik (nilai=4), MAX untuk pertanyaan ini juga 4
        $this->createJawabanSkm('R01', '10001', '2025-01-15', $pilihanBaik);

        $result = $this->service->getSkmData(1, 2025);

        // jumlah_sesuai = nilai pilihan = 4
        $this->assertEquals(4, $result['jumlah_sesuai']);
        // jumlah_total = MAX(nilai) pertanyaan ini = 4 (kita buat sendiri, max = 4)
        $this->assertEquals(4, $result['jumlah_total']);
        // persen = 4/4 * 100 = 100%
        $this->assertEquals(100.0, $result['persen']);
    }

    // U48 - getSkmData() mengisi byTanggal dengan key berupa hari (format 'j')
    public function test_get_skm_data_groups_by_tanggal_with_day_key()
    {
        [, $pilihanBaik] = $this->createStrukturSkm();

        $this->createJawabanSkm('R01', '10002', '2025-01-15', $pilihanBaik);
        $this->createJawabanSkm('R01', '10003', '2025-01-20', $pilihanBaik);

        $result = $this->service->getSkmData(1, 2025);

        // Key byTanggal menggunakan format 'j' (hari tanpa leading zero)
        $this->assertArrayHasKey(15, $result['byTanggal']);
        $this->assertArrayHasKey(20, $result['byTanggal']);
    }

    // U49 - getSkmData() setiap entry byTanggal adalah object dengan pasien_sesuai & total_pasien
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

    // U50 - getSkmData() mengagregasi multiple jawaban pada hari yang sama
    public function test_get_skm_data_aggregates_multiple_answers_on_same_day()
    {
        [, $pilihanBaik, $pilihanCukup] = $this->createStrukturSkm();

        // Dua pasien di hari yang sama: nilai=4 dan nilai=2 → jumlah_sesuai = 6
        // MAX untuk pertanyaan ini = 4, dua jawaban → jumlah_total = 4+4 = 8
        // persen = 6/8 * 100 = 75%
        $this->createJawabanSkm('R01', '10005', '2025-03-05', $pilihanBaik);
        $this->createJawabanSkm('R01', '10006', '2025-03-05', $pilihanCukup);

        $result = $this->service->getSkmData(3, 2025);

        $this->assertEquals(6, $result['jumlah_sesuai']);
        $this->assertEquals(8, $result['jumlah_total']);
        $this->assertEquals(75.0, $result['persen']);

        // Harus ada tepat 1 entry byTanggal (hari ke-5)
        $this->assertCount(1, $result['byTanggal']);
        $this->assertArrayHasKey(5, $result['byTanggal']);
    }

    // =========================================================================
    // buildChartSeriesForRuangan()
    // =========================================================================

    // U44 - buildChartSeriesForRuangan() mengembalikan array kosong jika tidak ada data mutu
    // (sudah ada sebelumnya, dipertahankan)
    public function test_build_chart_series_returns_empty_when_no_data()
    {
        $result = $this->service->buildChartSeriesForRuangan('R01', 2025);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    // U45 - buildChartSeriesForRuangan() mengembalikan 12 slot monthly per indikator
    // (sudah ada sebelumnya, dipertahankan)
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
        // Bulan Maret = index 2 → 80%
        $this->assertEquals(80.0, $result[0]['monthly'][2]);
    }

    // U51 - buildChartSeriesForRuangan() mengisi slot bulan yang ada dengan persentase benar
    public function test_build_chart_series_fills_correct_month_slot_with_percentage()
    {
        $ir = $this->createFreshIndikatorRuangan('R01', 'Indikator APD');

        // Data hanya di bulan Maret (index 2)
        MutuRuangan::create([
            'id_indikator_ruangan' => $ir->id_indikator_ruangan,
            'tanggal' => '2025-03-15',
            'pasien_sesuai' => 9,
            'total_pasien' => 10,
        ]);

        $result = $this->service->buildChartSeriesForRuangan('R01', 2025);

        $this->assertEquals(90.0, $result[0]['monthly'][2]);

        // Bulan lain yang tidak ada data harus null
        $this->assertNull($result[0]['monthly'][0]);  // Januari
        $this->assertNull($result[0]['monthly'][11]); // Desember
    }

    // U52 - buildChartSeriesForRuangan() mengembalikan key label, monthly, dan kategori
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

        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('label', $result[0]);
        $this->assertArrayHasKey('monthly', $result[0]);
        $this->assertArrayHasKey('kategori', $result[0]);
        $this->assertEquals('Indikator Keselamatan Pasien', $result[0]['label']);
    }

    // U53 - buildChartSeriesForRuangan() mengagregasi beberapa hari dalam bulan yang sama
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

        // monthly index 1 = Februari
        $this->assertEquals(70.0, $result[0]['monthly'][1]);
    }

    // U54 - buildChartSeriesForRuangan() hanya mengembalikan data ruangan yang diminta
    public function test_build_chart_series_isolates_data_by_ruangan()
    {
        // Data R01
        $irR01 = $this->createFreshIndikatorRuangan('R01', 'Indikator R01 Isolasi');
        MutuRuangan::create([
            'id_indikator_ruangan' => $irR01->id_indikator_ruangan,
            'tanggal' => '2025-04-10',
            'pasien_sesuai' => 8,
            'total_pasien' => 10,
        ]);

        // Data R02
        $irR02 = $this->createFreshIndikatorRuangan('R02', 'Indikator R02 Isolasi');
        MutuRuangan::create([
            'id_indikator_ruangan' => $irR02->id_indikator_ruangan,
            'tanggal' => '2025-04-10',
            'pasien_sesuai' => 5,
            'total_pasien' => 10,
        ]);

        // Query hanya untuk R01 — harus dapat 1 series saja
        $result = $this->service->buildChartSeriesForRuangan('R01', 2025);

        $this->assertCount(1, $result);
        $this->assertEquals('Indikator R01 Isolasi', $result[0]['label']);
    }

    // =========================================================================
    // switchIndikatorRuangan()
    // =========================================================================

    // U55 - switchIndikatorRuangan() menonaktifkan indikator lama
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

    // U56 - switchIndikatorRuangan() mengaktifkan indikator baru
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

    // U57 - switchIndikatorRuangan() berjalan dalam satu transaksi dan return true
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

        // Method return true (dari dalam DB::transaction closure)
        $this->assertTrue($result);

        // Verifikasi atomik — kedua perubahan harus ada bersamaan
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

    // U58 - switchIndikatorRuangan() mengaktifkan kembali record nonaktif yang sudah ada
    // (tidak membuat record duplikat)
    public function test_switch_indikator_reactivates_existing_inactive_record()
    {
        $irLama = $this->createFreshIndikatorRuangan('R01', 'Indikator Reaktivasi Lama');

        // Indikator baru yang sudah pernah ada tapi nonaktif
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

        // Tidak boleh ada duplikat record aktif untuk indikator yang sama
        $count = IndikatorRuangan::where('id_ruangan', 'R01')
            ->where('id_indikator', $indikatorBaru->id_indikator)
            ->where('active', true)
            ->count();

        $this->assertEquals(1, $count);
    }
}