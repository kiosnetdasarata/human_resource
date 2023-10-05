<?php

namespace App\Http\Requests\Employee;

use Illuminate\Validation\Rules\File;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

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
            'foto_profile' => 'required', File::image(),

            'pendidikan_terakhir' => 'required|string',
            'nik' => 'required|digits:16|unique:employees,nik',
            'nickname' => 'required|string',
            'tgl_lahir' => 'required|date_format:Y-m-d',
            'tempat_lahir' => 'required|string',
            'almt_detail' => 'required|string',
            'nama_instansi' => 'required|string',
            'tahun_lulus' => 'required|digits:4',
            'tgl_mulai_kerja' =>'required|date_format:Y-m-d',
            'komisi_id' => 'exists:commissions,id',
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
