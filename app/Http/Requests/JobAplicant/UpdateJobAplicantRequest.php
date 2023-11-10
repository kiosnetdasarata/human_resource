<?php

namespace App\Http\Requests\JobAplicant;

use App\Rules\SocialMediaLink;
use Illuminate\Validation\Rules\File;
use Illuminate\Foundation\Http\FormRequest;

class UpdateJobAplicantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->method('patch');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'vacancy_id' => 'in:job_vacancies,id,is_active,1',
            'nama_lengkap' => 'string',
            'tanggal_lahir' => 'date:Y-m-d',
            'jk' => 'in:Laki-Laki,Perempuan',
            'alamat' => 'string',
            'email' => 'email|unique:internships,email|unique:employee_personal_informations,email',
            'no_tlpn' => 'numeric|digits_between:10,15',
            'pendidikan_terakhir' => 'required|string',
            'nama_instansi' => 'required|string',
            'tahun_lulus' => 'required_if:digits,4',
            'link_sosmed' => ['url', new SocialMediaLink], //wajib pake https://www.
            'role_id' => 'exists:job_vacancies,role_id,is_active,1',
            'pengalaman' => 'string',
            'ekspetasi_gaji' => 'string',
            'file_cv' => [File::types(['pdf'])->max(5 * 1024),],
            'link_portofolio' => 'url',
            'sumber_info' => 'string',
        ];
        
    }
}
