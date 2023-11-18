<?php

namespace App\Http\Requests\JobAplicant;

use App\Rules\SocialMediaLink;
use Illuminate\Validation\Rules\File;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreJobAplicantRequest extends FormRequest
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
            'vacancy_id' => 'required|in:job_vacancies,id,is_active,1,is_intern,0',
            'nama_lengkap' => 'required|string',
            'tanggal_lahir' => 'required|date:Y-m-d',
            'jk' => 'required|in:Laki-Laki,Perempuan',
            'alamat' => 'required|string',
            'email' => 'required|email|unique:internships,email|unique:employee_personal_informations,email',
            'no_tlpn' => 'required|numeric|digits_between:10,15',
            'pendidikan_terakhir' => 'required|string',
            'nama_instansi' => 'required|string',
            'tahun_lulus' => 'required_if:digits,4',
            'link_sosmed' => ['required', 'url', new SocialMediaLink], //wajib pake https://www.
            'pengalaman' => 'required|string',
            'ekspetasi_gaji' => 'required|string',
            'file_cv' => ['required', File::types(['pdf'])->max(5 * 1024),],
            'link_portofolio' => 'required|url',
            'sumber_info' => 'required|string',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
                'input' => $this->input(),
                'status_code' => 422,
            ])
        );
    }
}
