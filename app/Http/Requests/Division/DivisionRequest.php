<?php

namespace App\Http\Requests\Division;

use Illuminate\Foundation\Http\FormRequest;

class DivisionRequest extends FormRequest
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
            'nama_divisi' => ['required','iunique:divisions,nama_divisi,'.$this->route('division')],
        ];
    }

    public function messages()
    {
        return [
            'nama_divisi.iunique' => 'The nama divisi has already been taken',
        ];
    }
}
