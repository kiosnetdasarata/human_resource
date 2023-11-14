<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
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
            'divisi_id' => 'required|exists:divisions,id',
            'kode_jabatan' => 'required|unique:roles,kode_jabatan',
            'nama_jabatan' => 'required|unique:roles,nama_jabatan',
            // 'level_id' => 'required|exists:levels,id',
            'deskripsi' => 'required|string',
        ];
    }
}
