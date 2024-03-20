<?php

namespace App\Http\Requests\Employee;

use App\Models\Employee;
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
        $employee = Employee::find($this->route('employee'));
        return $this->method() == 'PATCH' && $employee && $this->user()->can('update', $employee);
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
            'alamat_sekarang' => 'string',
            
            'nama_bank' => 'string',
            'nomor_rekening' => 'numeric',
            'no_tlpn_darurat' => 'string|digits_between:10,15',
            'nama_kontak_darurat' => 'string',
            'status_kontak_darurat' => 'string',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        // throw new HttpResponseException(
        //     response()->json([
        //         'status' => 'error',
        //         'errors' => $validator->errors(),
        //         'input' => $this->input(),
        //         'status_code' => 422,
        //     ])
        // );
    }
}
