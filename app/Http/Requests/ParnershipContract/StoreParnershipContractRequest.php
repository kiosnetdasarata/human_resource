<?php

namespace App\Http\Requests\ParnershipContract;

use Illuminate\Validation\Rules\File;
use Illuminate\Foundation\Http\FormRequest;

class StoreParnershipContractRequest extends FormRequest
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
            'mitra_id' => 'required|exists:partnership,id',
            'file_mou' => ['required', File::types(['pdf'])->max(5 * 1024)],
            'file_moa' => ['required', File::types(['pdf'])->max(5 * 1024)],
            'date_start' => 'required|date_format:Y-m-d',
            'date_expired' => 'required|date_format:Y-m-d',
            'durasi' => 'required|integer'
        ];
    }
}
