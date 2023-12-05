<?php

namespace App\Http\Requests\Traineeship;

use App\Rules\SocialMediaLink;
use Illuminate\Validation\Rules\File;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTraineeshipRequest extends FormRequest
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
            'nama_lengkap' => 'exclude_with:status_tahap|string',
            'vacancy_id' => 'exclude_with:status_tahap|exists:job_vacancies,id,is_active,1,is_intern,1',
            'jk' => 'exclude_with:status_tahap|in:Laki-Laki,Perempuan',
            'nomor_telepone' => 'exclude_with:status_tahap|numeric|digits_between:10,15',
            'email' => 'exclude_with:status_tahap|email|unique:internships,email|unique:employee_personal_informations,email',
            'alamat' => 'exclude_with:status_tahap|string',
            'link_sosmed' => ['exclude_with:status_tahap','url', new SocialMediaLink], //wajib pake https://www.
            'is_kuliah' => 'exclude_with:status_tahap|in:0,1',
            'nama_instansi' => 'exclude_with:status_tahap|string',
            'semester' => 'exclude_with:status_tahap|numeric|max:20',
            'tahun_lulus' => 'exclude_with:status_tahap|required_if:is_kuliah,0',
            'durasi' => 'exclude_with:status_tahap|in:3,6',
            'status_tahap' => 'in:FU,Assesment,Lolos,Tolak',
            'file_cv' => ['exclude_with:status_tahap',File::types(['pdf'])->max(5 * 1024),],
            'link_portofolio' => 'exclude_with:status_tahap|url',
            'sumber_info' => 'exclude_with:status_tahap|string'
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
