<?php

namespace App\Http\Requests\Level;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateLevelRequest extends FormRequest
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
        $level = $this->route('level');
        return [
            'kode_level' => 'unique:levels,kode_level,'. $level. ',kode_level',
            'nama_level' => 'unique:levels,nama_level,'. $level. ',kode_level',
            'deskripsi' => 'string'
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
