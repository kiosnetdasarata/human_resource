<?php

namespace App\Http\Requests\Parnership;

use Illuminate\Foundation\Http\FormRequest;

class StoreParnershipRequest extends FormRequest
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
            'nama_mitra' => 'required|string',
            'alamat' => 'required|string',
            'perwakilan_mitra' => 'required|string',
            'no_telp' => 'required|unique:partnerships,no_telp|min:10|max:15',
            'katergori_mitra' => 'required|string',
        ];
    }
}
