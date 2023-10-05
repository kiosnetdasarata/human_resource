<?php

namespace App\Http\Requests\Traineeship;

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
            'role_id' => 'exists:roles,id',
            'durasi' => 'in:3,6',
            'email' => 'email|unique:traineeship,email,'.$traineeship.',id',
            'nomor_telepone' => 'unique:traineeships,nomor_telepone,'.$traineeship.',id|min:10|max:15',
            'alamat' => 'string',
            'tanggal_lamaran' => 'date_format:Y-m-d',
            'status_traineeship' => 'string',
            'file_cv' => 'file_type:pdf|file_size:5000',
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
