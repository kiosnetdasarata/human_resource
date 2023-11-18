<?php

namespace App\Http\Requests\InterviewPoint;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateInterviewPointRequest extends FormRequest
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
            'presentasi' => 'digits_between:1,3',
            'kualitas_kerja' => 'digits_between:1,3',
            'etika' => 'digits_between:1,3',
            'adaptif' => 'digits_between:1,3',
            'kerja_sama' => 'digits_between:1,3',
            'disiplin' => 'digits_between:1,3',
            'tanggung_jawab' => 'digits_between:1,3',
            'inovatif_kreatif' => 'digits_between:1,3',
            'problem_solving' => 'digits_between:1,3',
            'kemampuan_teknis' => 'digits_between:1,3',
            'tugas' => 'digits_between:1,3',
            'keterangan_hr' => 'string',
            'keterangan_user' => 'string',
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
