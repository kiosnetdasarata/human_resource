<?php

namespace App\Http\Requests\Partnership;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePartnershipRequest extends FormRequest
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
        $partnership = $this->route('partnership');
        return [
            'nama_mitra' => 'string|unique:partnerships,nama_mitra,'.$partnership.',id',
            'alamat' => 'string',
            'perwakilan_mitra' => 'string',
            'no_tlpn' => 'numeric|digits_between:10,15|unique:partnerships,no_tlpn,'.$partnership.',id',
            'kategori_mitra' => 'in:Universitas,SMK,Bootcamp',
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
