<?php

namespace App\Http\Requests\Employee;

use Illuminate\Validation\Rule;
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
        return [
            'branch_company_id' => 'int|exists:mysql3.branch_companies,id',
            // 'jabatan_id' => 'int|exists:job_titles,id',
            // 'status_level_id' => 'integer|exists:status_levels,id',
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
            'village_id' => 'numeric|exists:villages,id',
            'status_perkawinan' => 'string|in:Belum Kawin,Kawin',
            'pendidikan_terakhir' => 'string',
            'nama_instansi' => 'string',
            'tahun_lulus' => 'digits:4',
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
