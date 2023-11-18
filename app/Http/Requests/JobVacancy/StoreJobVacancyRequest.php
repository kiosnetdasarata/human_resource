<?php

namespace App\Http\Requests\JobVacancy;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreJobVacancyRequest extends FormRequest
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
            'title' => 'required|string',
            'min_umur' => 'required|digits_between:1,2',
            'max_umur' => 'required|digits_between:1,2',
            'branch_company_id' => 'required|int',
            'role_id' => 'required|exists:roles,id',
            'open_date' => 'required|date:Y-m-d',
            'close_date' => 'required|date:Y-m-d',
            'is_active' => 'required|in:0,1',
            'is_intern' => 'required|in:0,1',
            'keterangan' => 'required|string',
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
