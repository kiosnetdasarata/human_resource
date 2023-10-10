<?php

namespace App\Http\Requests\Parnership;

use Illuminate\Foundation\Http\FormRequest;

class UpdateParnershipRequest extends FormRequest
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
            'nama_mitra' => 'string',
            'alamat' => 'string',
            'perwakilan_mitra' => 'string',
            'no_telp' => 'unique:partnerships,no_telp|min:10|max:15',
            'katergori_mitra' => 'string',
        ];
    }
}
