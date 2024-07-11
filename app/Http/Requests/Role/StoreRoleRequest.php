<?php

namespace App\Http\Requests\Role;

use Illuminate\Support\Str;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreRoleRequest extends FormRequest
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
            'kode_jabatan' => str_replace(' ', '', strtoupper($this->kode_jabatan)),
            'nama_jabatan' => Str::title($this->nama_jabatan),
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
            'divisi_id' => 'required|exists:divisions,id,is_active,1',
            'kode_jabatan' => 'required|unique:roles,kode_jabatan',
            'nama_jabatan' => 'required|unique:roles,nama_jabatan',
            'deskripsi' => 'required|string',
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
