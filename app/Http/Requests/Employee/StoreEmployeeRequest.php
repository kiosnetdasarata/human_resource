<?php

namespace App\Http\Requests\Employee;

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
        return [
            'branch_company_id' => 'required|int',
            // 'divisi_id' => 'required|int|exists:divisions,id',
            'jabatan_id' => 'required|int|exists:job_titles,id',
            'status_level_id' => 'required|integer|exists:status_levels,id',
            'no_tlpn' => 'required|string|min:10|max:15|unique:employees,no_tlpn',
            'email' => 'required|email',
            'nik' => 'required|digits:16|unique:employees,nik',
            'nama' => 'required|string',
            'nickname' => 'required|string',
            'agama' => 'required|in:Islam,Kristen,Katolik,Budha,Hindu',
            'jk' => 'required|in:Laki-Laki,Perempuan',
            'tgl_lahir' => 'required|date_format:Y-m-d',
            'tempat_lahir' => 'required|string',
            'almt_detail' => 'required|string',
            // 'province_id' => 'required|numeric|exists:provinces,id',
            // 'regencie_id' => 'required|numeric|exists:regencies,id',
            // 'district_id' => 'required|numeric|exists:districts,id',
            'village_id' => 'required|numeric|exists:villages,id',
            'status_perkawinan' => 'required|string|in:Belum Kawin,Kawin',
            'pendidikan_terakhir' => 'required|string',
            'nama_instansi' => 'required|string',
            'tahun_lulus' => 'required|digits:4',
            'tgl_mulai_kerja' =>'required|date_format:Y-m-d'
        ];
    }
}
