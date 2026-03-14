<?php

namespace Tests\Feature\Admin;

use App\Http\Requests\StoreInputMutuRequest;
use App\Models\IndikatorMutu;
use App\Models\IndikatorRuangan;
use App\Models\Kategori;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class InputIndikatorControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected IndikatorMutu $indikator1;
    protected IndikatorMutu $indikator2;
    protected IndikatorRuangan $ir1;
    protected IndikatorRuangan $ir2;

    protected function setUp(): void
    {
        parent::setUp();

        Ruangan::firstOrCreate(['id_ruangan' => 'R01'], ['nama_ruangan' => 'Ruangan A']);
        Kategori::firstOrCreate(['id_kategori' => 1], ['kategori' => 'Indikator Nasional Mutu']);

        $this->admin = User::create([
            'id_user' => 'U001',
            'id_ruangan' => 'R01',
            'username' => 'admin_input',
            'password' => Hash::make('password'),
            'nama_ruangan' => 'Ruangan A',
        ]);

        $this->indikator1 = IndikatorMutu::create([
            'id_kategori' => 1,
            'variabel' => 'Indikator 1',
            'standar' => '90',
        ]);
        $this->indikator2 = IndikatorMutu::create([
            'id_kategori' => 1,
            'variabel' => 'Indikator 2',
            'standar' => '85',
        ]);

        $this->ir1 = IndikatorRuangan::create([
            'id_ruangan' => 'R01',
            'id_indikator' => $this->indikator1->id_indikator,
            'active' => true,
        ]);
        $this->ir2 = IndikatorRuangan::create([
            'id_ruangan' => 'R01',
            'id_indikator' => $this->indikator2->id_indikator,
            'active' => true,
        ]);
    }

    // =========================================================================
    // create() — tampil form
    // =========================================================================

    // F17 - Admin bisa melihat form input (200)
    public function test_admin_can_view_input_form()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.input_indikator'));
        $response->assertStatus(200);
    }

    // F22 - Guest tidak bisa akses → redirect route('login')
    public function test_guest_cannot_access_input_indikator()
    {
        $response = $this->get(route('admin.input_indikator'));
        $response->assertRedirect(route('login'));
    }

    // F23 - Form hanya menampilkan indikator aktif milik ruangan user
    public function test_form_shows_only_active_indikators_for_user_ruangan()
    {
        $indikatorNonaktif = IndikatorMutu::create([
            'id_kategori' => 1,
            'variabel' => 'Indikator Nonaktif',
            'standar' => '80',
        ]);

        IndikatorRuangan::create([
            'id_ruangan' => 'R01',
            'id_indikator' => $indikatorNonaktif->id_indikator,
            'active' => false,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.input_indikator'));

        $response->assertStatus(200);
        $response->assertSee('Indikator 1');
        $response->assertSee('Indikator 2');
        $response->assertDontSee('Indikator Nonaktif');
    }

    // =========================================================================
    // store() — HTTP submit
    // =========================================================================

    // F18 - Admin bisa submit data array yang valid → assertDatabaseHas
    public function test_admin_can_submit_valid_array_data()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.input_indikator.store'), [
            'tanggal' => '2025-01-15',
            'pasien_sesuai' => [
                $this->indikator1->id_indikator => 8,
                $this->indikator2->id_indikator => 7,
            ],
            'total_pasien' => [
                $this->indikator1->id_indikator => 10,
                $this->indikator2->id_indikator => 10,
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('mutu_ruangan', [
            'id_indikator_ruangan' => $this->ir1->id_indikator_ruangan,
            'tanggal' => '2025-01-15',
            'pasien_sesuai' => 8,
            'total_pasien' => 10,
        ]);
    }

    // F19 - Submit gagal jika tanggal tidak ada → sessionHasErrors('tanggal')
    public function test_submission_fails_when_tanggal_missing()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.input_indikator.store'), [
            'pasien_sesuai' => [$this->ir1->id_indikator_ruangan => 8],
            'total_pasien' => [$this->ir1->id_indikator_ruangan => 10],
        ]);

        $response->assertSessionHasErrors('tanggal');
    }

    // F20 - Submit gagal jika pasien_sesuai > total_pasien
    public function test_submission_fails_when_sesuai_exceeds_total()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.input_indikator.store'), [
            'tanggal' => '2025-01-15',
            'pasien_sesuai' => [$this->ir1->id_indikator_ruangan => 15],
            'total_pasien' => [$this->ir1->id_indikator_ruangan => 10],
        ]);

        $response->assertSessionHasErrors();
    }

    // F21 - Submit gagal dengan nilai negatif
    public function test_submission_fails_with_negative_values()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.input_indikator.store'), [
            'tanggal' => '2025-01-15',
            'pasien_sesuai' => [$this->ir1->id_indikator_ruangan => -1],
            'total_pasien' => [$this->ir1->id_indikator_ruangan => 10],
        ]);

        $response->assertSessionHasErrors();
    }

    // =========================================================================
    // StoreInputMutuRequest — validasi rules langsung
    // =========================================================================

    private function validate(array $data): \Illuminate\Validation\Validator
    {
        $request = new StoreInputMutuRequest();
        return Validator::make($data, $request->rules(), $request->messages());
    }

    // U25 - Validasi rules: pass dengan data valid
    public function test_validation_passes_with_valid_data()
    {
        $validator = $this->validate([
            'tanggal' => '2025-01-15',
            'pasien_sesuai' => [1 => 8, 2 => 5],
            'total_pasien' => [1 => 10, 2 => 10],
        ]);

        $this->assertFalse($validator->fails());
    }

    // U26 - Validasi rules: gagal jika tanggal missing
    public function test_validation_fails_when_tanggal_is_missing()
    {
        $validator = $this->validate([
            'pasien_sesuai' => [1 => 8],
            'total_pasien' => [1 => 10],
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('tanggal', $validator->errors()->toArray());
    }

    // U27 - Validasi rules: gagal jika pasien_sesuai negatif
    public function test_validation_fails_when_pasien_sesuai_is_negative()
    {
        $validator = $this->validate([
            'tanggal' => '2025-01-15',
            'pasien_sesuai' => [1 => -1],
            'total_pasien' => [1 => 10],
        ]);

        $this->assertTrue($validator->fails());
    }

    // U28 - Validasi rules: gagal jika total_pasien negatif
    public function test_validation_fails_when_total_pasien_is_negative()
    {
        $validator = $this->validate([
            'tanggal' => '2025-01-15',
            'pasien_sesuai' => [1 => 5],
            'total_pasien' => [1 => -1],
        ]);

        $this->assertTrue($validator->fails());
    }

    // U29 - Validasi withValidator: gagal jika sesuai > total (via HTTP)
    public function test_validation_fails_when_sesuai_exceeds_total_via_http()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.input_indikator.store'), [
            'tanggal' => '2025-01-15',
            'pasien_sesuai' => [1 => 15],
            'total_pasien' => [1 => 10],
        ]);

        $response->assertSessionHasErrors();
    }
}