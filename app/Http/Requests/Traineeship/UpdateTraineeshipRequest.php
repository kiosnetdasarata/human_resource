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
        $traineeship = $this->route('traineeship');
        return [
            'nama_lengkap' => 'string',
            'jk' => 'in:Laki-Laki,Perempuan',
            'nomor_telepone' => 'numeric|digits_between:10,15',
            'email' => 'email|unique:traineeships,email,'. $traineeship,
            'alamat' => 'string',
            'link_sosmed' => ['url', new SocialMediaLink], //wajib pake https://www.
            'is_kuliah' => 'in:0,1',
            'nama_instansi' => 'string',
            'semester' => 'numeric|max:20',
            'tahun_lulus' => 'required_if:is_kuliah,0',
            'role_id' => 'exists:job_vacancies,role_id,is_active,1',
            'durasi' => 'in:3,6',
            'file_cv' => [File::types(['pdf'])->max(5 * 1024),],
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
