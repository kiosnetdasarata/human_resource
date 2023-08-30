<?php

namespace App\Http\Requests\JobTitle;

use Illuminate\Foundation\Http\FormRequest;

class JobTitleRequest extends FormRequest
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
            'nama_jabatan' => ['required','iunique:job_titles,nama_jabatan,'.$this->route('job-title')],
            'divisions_id' => 'required|exists:divisions,id'
        ];
    }

    public function messages()
    {
        return [
            'nama_jabatan.iunique' => 'The nama jabatan has already been taken',
        ];
    }
}
