<?php

namespace App\Http\Requests\Division;

use Illuminate\Support\Str;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreDivisionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
        $this->merge([
            'kode_divisi' => str_replace(' ', '', strtoupper($this->kode_divisi)),
            'nama_divisi' => Str::title($this->nama_divisi),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'nama_divisi' => 'required|unique:divisions,nama_divisi',
            'kode_divisi' => 'required|string|unique:divisions,kode_divisi',
            'manager_divisi' => 'required|exists:employee_personal_informations,nip|unique:divisions,manager_divisi',
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
