<?php

namespace App\Http\Requests\StatusLevel;

use Illuminate\Foundation\Http\FormRequest;

class StatusLevelRequest extends FormRequest
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
            'nama_level' => ['required','iunique:status_levels,nama_level,'.$this->route('level')],
        ];
    }

    public function messages()
    {
        return [
            'nama_level.iunique' => 'The nama level has already been taken',
        ];
    }
}
