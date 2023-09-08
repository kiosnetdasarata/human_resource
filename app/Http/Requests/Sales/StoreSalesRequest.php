<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;

class StoreSalesRequest extends FormRequest
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
            'karyawan_nip' => 'required|integer|unique:sales,karyawan_nip|exists:employee,nip_pgwi',
            'komisi_id' => 'required|integer|exists:commissions,id',
            'level_id' => 'required|integer|exists:levels,id',
        ];
    }
}
