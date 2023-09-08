<?php

namespace App\Http\Requests\Employee;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->isMethod('patch');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $employee = $this->route('employee');
        $rules = [
            'branch_company_id' => 'int',
            'divisi_id' => 'int',
            'jabatan_id' => 'int',
            'status_level_id' => 'integer',
            'no_tlpn' => 'string|min:10|max:15|unique:employees,no_tlpn,' . $employee.',uuid',
            'email' => 'email',
            'nik' => 'digits:16|unique:employees,nik,'. $employee. ',uuid',
            'nama' => 'string',
            'nickname' => 'string',
            'agama' => 'in:Islam,Kristen,Katolik,Budha,Hindu',
            'jk' => 'in:Laki-Laki,Perempuan',
            'tgl_lahir' => 'date_format:Y-m-d',
            'tempat_lahir' => 'string',
            'almt_detail' => 'string',
            'province_id' => 'numeric',
            'regencie_id' => 'numeric',
            'district_id' => 'numeric',
            'village_id' => 'numeric',
            'status_perkawinan' => 'string',
            'pendidikan_terakhir' => 'string',
            'nama_instansi' => 'string',
            'tahun_lulus' => 'digits:4',
        ];

        return $rules;
    }
}
