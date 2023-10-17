<?php

namespace App\Http\Requests\InternshipContract;

use Illuminate\Foundation\Http\FormRequest;

class StoreIntershipContractRequest extends FormRequest
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
            'internship_nip_id' => 'required|exists:internship,internship_nip',
            'nomor_kontrak' => 'required|integer',
            'durasi_kontrak' => 'required|in:3,6',
            'date_start' => 'required|date_format:Y-m-d',
            'date_expired' => 'required|date_format:Y-m-d',
        ];
    }
}
