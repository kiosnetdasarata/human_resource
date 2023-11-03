<?php

namespace App\Http\Requests\Employee;

use Illuminate\Validation\Rules\File;
use Illuminate\Foundation\Http\FormRequest;

class StoreContractRequest extends FormRequest
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
            'nomor_kontrak' => 'required|numeric|unique:employee_contracts,nomor_kontrak',
            'jenis_kontrak' => 'required|string',
            'start_kontrak' => 'required|date_format:Y-m-d',
            'end_kontrak' => 'required|date_format:Y-m-d',
            'work_start' => 'required|date_format:Y-m-d',
            'supervisor' => 'exists:employee_personal_informations,nip',
            'file_terms' => ['required', File::types(['pdf'])->max(5 * 1024),],
        ];
    }
}
