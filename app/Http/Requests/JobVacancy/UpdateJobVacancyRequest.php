<?php

namespace App\Http\Requests\JobVacancy;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateJobVacancyRequest extends FormRequest
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
            'close_date' => 'date:Y-m-d',
            'is_active' => 'in:0,1',
            'branch_company_id' => 'int',
            'role_id' => 'exists:roles,id',
            'title' => 'string',
            'min_umur' => 'digits_between:1,2',
            'max_umur' => 'digits_between:1,2',
            'keterangan' => 'string',
            'open_date' => 'date:Y-m-d',
            'is_intern' => 'in:0,1'
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
