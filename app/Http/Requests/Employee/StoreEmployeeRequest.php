<?php

namespace App\Http\Requests\Employee;

use Illuminate\Validation\Rule;
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
            'branch_company_id' => 'required|int|exists:mysql3.branch_companies,id',
            'jabatan_id' => 'required|int|exists:job_titles,id',
            'status_level_id' => 'required|integer|exists:status_levels,id',
            'no_tlpn' => 'required|string|min:10|max:15|unique:employees,no_tlpn',
            'email' => 'required|email|unique:employees,email',
            'nik' => 'required|digits:16|unique:employees,nik',
            'nama' => 'required|string',
            'nickname' => 'required|string',
            'agama' => 'required|in:Islam,Kristen,Katolik,Budha,Hindu',
            'jk' => 'required|in:Laki-Laki,Perempuan',
            'tgl_lahir' => 'required|date_format:Y-m-d',
            'tempat_lahir' => 'required|string',
            'almt_detail' => 'required|string',
            'village_id' => 'required|numeric|exists:villages,id',
            'status_perkawinan' => 'required|string|in:Belum Kawin,Kawin',
            'pendidikan_terakhir' => 'required|string',
            'nama_instansi' => 'required|string',
            'tahun_lulus' => 'required|digits:4',
            'tgl_mulai_kerja' =>'required|date_format:Y-m-d',
            'komisi_id' => 'integer|exists:commissions,id',
            'team_id' => 'integer|exists:mysql4.technician_teams,id',
            'katim_id' => 'integer|in:0,1',
            'is_leader' => 'required|in:0,1',
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
