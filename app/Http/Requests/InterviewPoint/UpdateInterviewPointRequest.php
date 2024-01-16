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
            'presentasi' => 'decimal:0,2',
            'kualitas_kerja' => 'decimal:0,2',
            'etika' => 'decimal:0,2',
            'adaptif' => 'decimal:0,2',
            'kerja_sama' => 'decimal:0,2',
            'disiplin' => 'decimal:0,2',
            'tanggung_jawab' => 'decimal:0,2',
            'inovatif_kreatif' => 'decimal:0,2',
            'problem_solving' => 'decimal:0,2',
            'kemampuan_teknis' => 'decimal:0,2',
            'tugas' => 'decimal:0,2',
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
