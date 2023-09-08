<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSalesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->isMethod('patch');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'karyawan_nip' => 'integer|unique:sales,karyawan_nip,'.$this->route('sales').'|exists:employee,nip_pgwi',
            'komisi_id' => 'integer|exists:commissions,id',
            'level_id' => 'integer|exists:levels,id',
        ];
    }
}
