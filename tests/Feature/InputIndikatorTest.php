<?php

namespace Tests\Feature;

use App\Models\IndikatorMutu;
use App\Models\IndikatorRuangan;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class InputIndikatorTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected IndikatorRuangan $indikatorRuangan1;
    protected IndikatorRuangan $indikatorRuangan2;
    protected IndikatorMutu $indikator1;
    protected IndikatorMutu $indikator2;

    protected function setUp(): void
    {
        parent::setUp();

        Ruangan::firstOrCreate(['id_ruangan' => 'R01'], ['nama_ruangan' => 'Ruangan A']);

        \App\Models\Kategori::firstOrCreate(['id_kategori' => 1], ['kategori' => 'Kategori A']);

        $this->admin = User::create([
            'id_user' => 'U001',
            'id_ruangan' => 'R01',
            'username' => 'admin',
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

        $this->indikatorRuangan1 = IndikatorRuangan::create([
            'id_ruangan' => 'R01',
            'id_indikator' => $this->indikator1->id_indikator,
            'active' => true,
        ]);
        $this->indikatorRuangan2 = IndikatorRuangan::create([
            'id_ruangan' => 'R01',
            'id_indikator' => $this->indikator2->id_indikator,
            'active' => true,
        ]);
    }

    // F17 - Admin bisa melihat form input
    public function test_admin_can_view_input_form()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.input_indikator'));
        $response->assertStatus(200);
    }

    // F18 - Admin bisa submit data array yang valid
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
            'id_indikator_ruangan' => $this->indikatorRuangan1->id_indikator_ruangan,
            'tanggal' => '2025-01-15',
            'pasien_sesuai' => 8,
            'total_pasien' => 10,
        ]);
    }

    // F19 - Submit gagal jika tanggal tidak ada
    public function test_submission_fails_when_tanggal_missing()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.input_indikator.store'), [
            'pasien_sesuai' => [$this->indikatorRuangan1->id_indikator_ruangan => 8],
            'total_pasien' => [$this->indikatorRuangan1->id_indikator_ruangan => 10],
        ]);

        $response->assertSessionHasErrors('tanggal');
    }

    // F20 - Submit gagal jika pasien_sesuai > total_pasien
    public function test_submission_fails_when_sesuai_exceeds_total()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.input_indikator.store'), [
            'tanggal' => '2025-01-15',
            'pasien_sesuai' => [$this->indikatorRuangan1->id_indikator_ruangan => 15],
            'total_pasien' => [$this->indikatorRuangan1->id_indikator_ruangan => 10],
        ]);

        $response->assertSessionHasErrors();
    }

    // F21 - Submit gagal dengan nilai negatif
    public function test_submission_fails_with_negative_values()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.input_indikator.store'), [
            'tanggal' => '2025-01-15',
            'pasien_sesuai' => [$this->indikatorRuangan1->id_indikator_ruangan => -1],
            'total_pasien' => [$this->indikatorRuangan1->id_indikator_ruangan => 10],
        ]);

        $response->assertSessionHasErrors();
    }

    // F22 - Guest tidak bisa akses (redirect ke login)
    public function test_guest_cannot_access_input_indikator()
    {
        $response = $this->get(route('admin.input_indikator'));
        $response->assertRedirect(route('login'));
    }

    // F23 - Form hanya menampilkan indikator aktif milik ruangan user
    public function test_form_shows_only_active_indikators_for_user_ruangan()
    {
        // Buat indikator non-aktif
        IndikatorMutu::create(['id_indikator' => 3, 'id_kategori' => 1, 'variabel' => 'Indikator Nonaktif', 'standar' => 80]);
        IndikatorRuangan::create([
            'id_indikator_ruangan' => 3,
            'id_ruangan' => 'R01',
            'id_indikator' => 3,
            'active' => false,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.input_indikator'));
        $response->assertStatus(200);
        $response->assertSee('Indikator 1');
        $response->assertSee('Indikator 2');
        $response->assertDontSee('Indikator Nonaktif');
    }
}
