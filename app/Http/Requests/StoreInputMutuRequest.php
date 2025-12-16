<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInputMutuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tanggal' => 'required|date',
            'pasien_sesuai' => 'required|array',
            'total_pasien' => 'required|array',
            'pasien_sesuai.*' => 'required|integer|min:0', 
            'total_pasien.*' => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'pasien_sesuai.*.min' => 'Input pasien sesuai tidak boleh angka negatif.',
            'total_pasien.*.min' => 'Input total pasien tidak boleh angka negatif.',

            'pasien_sesuai.*.integer' => 'Input harus berupa angka.',
            'total_pasien.*.integer' => 'Input harus berupa angka.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $pasienSesuai = $this->input('pasien_sesuai', []);
            $totalPasien = $this->input('total_pasien', []);

            foreach ($pasienSesuai as $id => $val) {
                $total = $totalPasien[$id] ?? 0;

                if ($val > $total) {
                    $validator->errors()->add(
                        'logic_error',
                        "Data tidak valid: Jumlah pasien sesuai ($val) tidak boleh lebih besar dari total pasien ($total)."
                    );
                }
            }
        });
    }
}