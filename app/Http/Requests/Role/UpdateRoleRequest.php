<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateRoleRequest extends FormRequest
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
        $role = $this->route('role');
        return [
            'divisi_id' => 'exists:divisions,id',
            'kode_jabatan' => 'unique:roles,kode_jabatan,'. $role . ',kode_jabatan',
            'nama_jabatan' => 'unique:roles,nama_jabatan,'. $role . ',kode_jabatan',
            'level_id' => 'exists:levels,id',
            'deskripsi' => 'string',
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
