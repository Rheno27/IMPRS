<?php

namespace Tests\Unit;

use App\Http\Requests\StoreInputMutuRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreInputMutuRequestTest extends TestCase
{
    use DatabaseTransactions;

    private function validate(array $data): \Illuminate\Validation\Validator
    {
        $request = new StoreInputMutuRequest();
        return Validator::make($data, $request->rules(), $request->messages());
    }

    // U25 - Validation passes with valid array data
    public function test_validation_passes_with_valid_data()
    {
        $data = [
            'tanggal' => '2025-01-15',
            'pasien_sesuai' => [1 => 8, 2 => 5],
            'total_pasien' => [1 => 10, 2 => 10],
        ];

        $validator = $this->validate($data);
        $this->assertFalse($validator->fails());
    }

    // U26 - Validation fails when tanggal is missing
    public function test_validation_fails_when_tanggal_is_missing()
    {
        $data = [
            'pasien_sesuai' => [1 => 8],
            'total_pasien' => [1 => 10],
        ];

        $validator = $this->validate($data);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('tanggal', $validator->errors()->toArray());
    }

    // U27 - Validation fails when pasien_sesuai has negative value
    public function test_validation_fails_when_pasien_sesuai_is_negative()
    {
        $data = [
            'tanggal' => '2025-01-15',
            'pasien_sesuai' => [1 => -1],
            'total_pasien' => [1 => 10],
        ];

        $validator = $this->validate($data);
        $this->assertTrue($validator->fails());
    }

    // U28 - Validation fails when total_pasien has negative value
    public function test_validation_fails_when_total_pasien_is_negative()
    {
        $data = [
            'tanggal' => '2025-01-15',
            'pasien_sesuai' => [1 => 5],
            'total_pasien' => [1 => -1],
        ];

        $validator = $this->validate($data);
        $this->assertTrue($validator->fails());
    }

    // U29 - Validation fails when pasien_sesuai exceeds total_pasien (withValidator logic)
    public function test_validation_fails_when_sesuai_exceeds_total()
    {
        // Ensure the room exists and create a user with the required nama_ruangan
        \App\Models\Ruangan::firstOrCreate(['id_ruangan' => 'R01'], ['nama_ruangan' => 'Ruangan A']);

        $user = User::create([
            'id_user' => 'U001',
            'id_ruangan' => 'R01',
            'nama_ruangan' => 'Ruangan A',
            'username' => 'admintest',
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($user);

        $response = $this->post(route('admin.input_indikator.store'), [
            'tanggal' => '2025-01-15',
            'pasien_sesuai' => [1 => 15],
            'total_pasien' => [1 => 10],
        ]);

        $response->assertSessionHasErrors();
    }
}
