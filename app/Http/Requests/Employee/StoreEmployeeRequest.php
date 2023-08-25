<?php

namespace App\Http\Requests\Employee;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
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
            'branch_company_id' => 'required|int',
            'divisi_id' => 'required|int',
            'jabatan_id' => 'required|int',
            'status_level_id' => 'required|integer',
            'no_tlpn' => 'required|string|min:10|max:15|unique:employees',
            'email' => 'required|email',
            'nik' => 'required|digits:16|unique:employees',
            'nama' => 'required|string',
            'nickname' => 'required|string',
            'agama' => 'required|in:Islam,Kristen,Katolik,Budha,Hindu',
            'jk' => 'required|in:Laki-Laki,Perempuan',
            'tgl_lahir' => 'required|date_format:d/m/Y',
            'tempat_lahir' => 'required|string',
            'almt_detail' => 'required|string',
            'province_id' => 'required|numeric',
            'regencie_id' => 'required|numeric',
            'district_id' => 'required|numeric',
            'village_id' => 'required|numeric',
            'status_perkawinan' => 'required|string',
            'pendidikan_terakhir' => 'required|string',
            'nama_instansi' => 'required|string',
            'tahun_lulus' => 'required|digits:4',
            'tgl_mulai_kerja' =>'required|date_format:d/m/Y'
        ];

        return $rules;
    }
}
