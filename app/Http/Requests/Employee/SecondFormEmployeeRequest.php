<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SecondFormEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'nomor_bpjs' => 'required|numeric|max:16',
            'nama_bank' => 'required|string',
            'nomor_bank' => 'required|numeric',
            
            'nomor_kontrak' => 'required|numeric|unique:employee_contracts,nomor_kontrak',
            'jenis_kontrak' => 'required|string',
            'start_kontrak' => 'required|date_format:Y-m-d',
            'end_kontrak' => 'required|date_format:Y-m-d',
            'work_start' => 'required|date_format:Y-m-d',
            'supervisor' => 'required|exists:employee_personal_informations',
            'file_terms' => 'required|file_type:pdf|file_size:5000',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => 'error',
                'errors' => $validator->errors()->all(),
                'input' => $this->input()
            ], 422)
        );
    }
}
