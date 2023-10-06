<?php

namespace App\Http\Requests\Employee;

use Illuminate\Validation\Rules\File;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class FirstFormEmployeeRequest extends FormRequest
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
            'branch_company_id' => 'required|exists:mysql3.branch_companies,id',
            'role_id' => 'required|exists:roles,id',
            'level_id' => 'required|exists:levels,id',
            'nama' => 'required|string',
            'alamat' => 'required|string',
            'dusun_id' => 'required|exists:villages,id',
            'tempat_lahir' => 'required|string',
            'tgl_lahir' => 'required|date_format:Y-m-d',    
            'jenis_kelamin' => 'required|in:Laki-Laki,Perempuan',
            'no_tlpn' => 'required|string|min:10|max:20|unique:employees,no_tlpn',
            'email' => 'required|email|unique:employees,email',
            'agama' => 'required|in:Islam,Kristen,Katolik,Budha,Hindu',
            'status_perkawinan' => 'required|in:Belum Kawin,Kawin',
            'foto_profil' => 'required|file_type:jpg,jpeg,png|file_size:5000',

            'nik' => 'required|digits:16|unique:employees,nik',
            'no_telp_darurat' => 'required|string|min:10|max:20|',
            'nama_kontak_darurat' => 'required|string',
            'status_kontak_darurat' => 'required|string',
            'foto_ktp' => 'required|file_type:jpg,jpeg,png|file_size:5000',
            'foto_kk' => 'required|file_type:jpg,jpeg,png|file_size:5000',
            'file_cv' => 'required|file_type:pdf|file_size:5000',

            'pendidikan_terakhir' => 'required|string',
            'nama_instansi' => 'required|string',
            'tahun_lulus' => 'required|digits:4',

            'level_sales_id' => 'exists:level_sales,id',
            'team_id' => 'exists:mysql4.technician_teams,id',           
            'katim_id' => 'in:0,1',
            'is_leader' => 'in:0,1',
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
