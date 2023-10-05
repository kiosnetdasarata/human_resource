<?php

namespace App\Http\Requests\Traineeship;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rules\File;
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
            'role_id' => 'required|exists:roles,id',
            'durasi' => 'required|in:3,6',
            'email' => 'required|email|unique:traineeships,email',
            'nomor_telepone' => 'required|unique:traineeships,nomor_telepone|min:10|max:15',
            'alamat' => 'required|string',
            'tanggal_lamaran' => 'required|date_format:Y-m-d',
            'status_traineeship' => 'required|string',
            'file_cv' => 'required', File::types(['pdf'])
                                ->max(5 * 1024),
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
