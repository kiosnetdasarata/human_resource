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
            'status_perkawinan' => 'in:Belum Menikah,Menikah',
            'foto_ktp' => [File::types(['jpg','jpeg','png'])->max(2 * 1024),],
            'foto_kk' => [File::types(['jpg','jpeg','png'])->max(2 * 1024),],
            
            'pendidikan_terakhir' => 'in:Sarjana,SMK/SMA,SMP',
            'tahun_lulus' => 'required_with:pendidikan_terakhir',
            'nama_instansi' => 'required_with:pendidikan_terakhir',
            
            'nama_bank' => 'string',
            'nomor_rekening' => 'numeric',
            'no_tlpn_darurat' => 'string|digits_between:10,15',
            'nama_kontak_darurat' => 'string',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
                'input' => $this->input(),
                'status_code' => 422,
            ])
        );
    }
}
