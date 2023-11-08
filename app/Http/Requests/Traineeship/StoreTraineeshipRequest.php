<?php

namespace App\Http\Requests\Traineeship;

use App\Rules\SocialMediaLink;
use Illuminate\Validation\Rules\File;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTraineeshipRequest extends FormRequest
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
            'nama_lengkap' => 'required|string',
            'jk' => 'required|in:Laki-Laki,Perempuan',
            'nomor_telepone' => 'required|numeric|digits_between:10,15',
            'email' => 'required|email|unique:internships,email|unique:employee_personal_informations,email',
            'alamat' => 'required|string',
            'link_sosmed' => ['required', 'url', new SocialMediaLink], //wajib pake https://www.
            'is_kuliah' => 'required|in:0,1',
            'nama_instansi' => 'required|string',
            'semester' => 'required_if:is_kuliah,1|numeric|max:20',
            'tahun_lulus' => 'required_if:is_kuliah,0',
            'role_id' => 'required|exists:job_vacancies,role_id,is_active,1',
            'durasi' => 'required|in:3,6',
            'file_cv' => ['required', File::types(['pdf'])->max(5 * 1024),],
            'link_portofolio' => 'url',
            'sumber_info' => 'required|string'
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
