<?php

namespace App\Http\Requests\Partnership;

use Illuminate\Validation\Rules\File;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFilePartnershipRequest extends FormRequest
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
            'file_mou' => File::types(['pdf'])->max(5 * 1024),
            'file_moa' => File::types(['pdf'])->max(5 * 1024),
            'date_start' => 'date_format:Y-m-d',
            'durasi' => 'integer'
        ];
    }
}