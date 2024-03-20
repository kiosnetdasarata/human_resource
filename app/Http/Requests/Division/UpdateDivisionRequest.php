<?php

namespace App\Http\Requests\Division;

use Illuminate\Support\Str;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateDivisionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->isMethod('PATCH');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $division = $this->route('division');
        return [
            'nama_divisi' => 'unique:divisions,nama_divisi,'.$division.',id',
            'kode_divisi' => 'string|unique:divisions,kode_divisi,'.$division.',id',
            'manager_divisi' => 'exists:employee_personal_informations,nip',
            'email' => 'email|unique:divisions,email,'.$division.',id',
            'no_tlpn' => 'numeric|digits_between:10,15|unique:divisions,no_tlpn,'.$division.',id',
            'status' => 'string',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => 'error',
                'errors' => $validator->errors()->all(),
                'input' => $this->input(),
                'status_code' => 422,
            ])
        );
    }
}
