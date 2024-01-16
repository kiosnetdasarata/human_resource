<?php

namespace App\Http\Requests\InterviewPoint;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreInterviewPointRequest extends FormRequest
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
            'presentasi' => 'required|decimal:0,2',
            'kualitas_kerja' => 'required|decimal:0,2',
            'etika' => 'required|decimal:0,2',
            'adaptif' => 'required|decimal:0,2',
            'kerja_sama' => 'required|decimal:0,2',
            'disiplin' => 'required|decimal:0,2',
            'tanggung_jawab' => 'required|decimal:0,2',
            'inovatif_kreatif' => 'required|decimal:0,2',
            'problem_solving' => 'required|decimal:0,2',
            'kemampuan_teknis' => 'required|decimal:0,2',
            'tugas' => 'required|decimal:0,2',
            'keterangan_hr' => 'required|string',
            'keterangan_user' => 'required|string',
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
