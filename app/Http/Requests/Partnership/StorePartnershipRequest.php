<?php

namespace App\Http\Requests\Partnership;

use Illuminate\Foundation\Http\FormRequest;

class StorePartnershipRequest extends FormRequest
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
            'nama_mitra' => 'required|string|unique:partnerships,nama_mitra',
            'alamat' => 'required|string',
            'perwakilan_mitra' => 'required|string',
            'no_tlpn' => 'required|numeric|digits_between:10,15|unique:partnerships,no_tlpn',
            'kategori_mitra' => 'required|in:Universitas,SMK,Bootcamp',
        ];
    }
}
