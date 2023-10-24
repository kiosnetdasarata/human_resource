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
            'no_tlpn' => 'required|string|digits_between:10,15|unique:employee_personal_informations,no_tlpn',
            'email' => 'required|email|unique:employee_personal_informations,email',
            'agama' => 'required|in:Islam,Kristen,Katolik,Budha,Hindu',
            'status_perkawinan' => 'required|in:Belum Menikah,Menikah',
            'foto_profil' => ['required', File::types(['jpg','jpeg','png'])->max(2 * 1024),],

            'nik' => 'required|digits:16|unique:employee_confidential_informations,nik',
            'no_tlpn_darurat' => 'required|string|digits_between:10,15',
            'nama_kontak_darurat' => 'required|string',
            'status_kontak_darurat' => 'required|in:Ayah,Ibu,Istri,Anak,Kakek,Nenek,Saudara',
            'foto_ktp' => ['required', File::types(['jpg','jpeg','png'])->max(2 * 1024),],
            'foto_kk' => ['required', File::types(['jpg','jpeg','png'])->max(2 * 1024),],
            'file_cv' => ['required', File::types(['pdf'])->max(5 * 1024),],

            'pendidikan_terakhir' => 'required|in:Sarjana,SMK/SMA,SMP',
            'nama_instansi' => 'required|string',
            'tahun_lulus' => 'required|digits:4',

            'level_sales_id' => 'exists:level_sales,id', //wajib diisi kalo divisi sales, kalo bukan diisi juga gak bakal masuk database
            'team_id' => 'exists:mysql4.technician_teams,id', //sama, tapi versi teknisi,
            'katim_id' => 'in:0,1', //kalo gak diisi otomatis 0
            'is_leader' => 'in:0,1', //sama
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
                'input' => $this->input()
            ], 422)
        );
    }
}
