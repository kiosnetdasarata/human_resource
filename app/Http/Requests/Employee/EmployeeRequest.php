<?php

namespace App\Http\Requests\Employee;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
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
        $rules = [
            'brach_company_id' => 'required|int',
            'divisi_id' => 'required|int',
            'jabatan_id' => 'required|int',
            // 'no_tlpn' => 'required|string|min:10|max:15',
            // 'nik' => 'required|string|size:16',
            // 'nama' => 'required|string',
            // 'jk' => ['required', Rule::in(['Laki-Laki', 'Perempuan'])],
            // 'province_id' => 'required|numeric',
            // 'regencie_id' => 'required|numeric',
            // 'district_id' => 'required|numeric',
            // 'village_id' => 'required|numeric',
            // 'almt_detail' => 'required|string',
            // 'tgl_lahir' => 'required|date_format:Y-m-d',
            // 'agama' => ['required', Rule::in(['Islam', 'Kristen', 'Katolik', 'Budha', 'Hindu'])],
            // 'status_perkawinan' => 'required|string',
        ];

        if ($this->method() === 'ADD') {
            $rules['nik'] = 'required|string|min:10|max:15|unique|employees';
            $rules['no_tlpn'] = 'required|string|size:16|unique|employees';
        }

        return $rules;
    }
}
