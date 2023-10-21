<?php

namespace App\Http\Requests\Employee;

use Illuminate\Validation\Rules\File;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->method('patch');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'alamat' => 'string',
            'no_tlpn' => 'string|digits_between:10,15',
            'email' => 'email',
            'foto_ktp' => [File::types(['jpg','jpeg','png'])->max(2 * 1024),],
            'foto_kk' => [File::types(['jpg','jpeg','png'])->max(2 * 1024),],
            'pendidikan_terakhir' => 'in:Sarjana,SMK/SMA,SMP',
            'tahun_lulus' => 'required_with|pendidikan_terakhir',
            'status_perkawinan' => 'required|in:Belum Menikah,Menikah',
            
            'nama_bank' => 'required|string',
            'nomor_rekening' => 'required|numeric',
            'no_tlpn_darurat' => 'required|string|digits_between:10,15',
            'nama_kontak_darurat' => 'required|string',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
                'input' => $this->input()
            ], 422)
        );
    }
}
