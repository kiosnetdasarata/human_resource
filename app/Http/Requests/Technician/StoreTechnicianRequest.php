<?php

namespace App\Http\Requests\Technician;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTechnicianRequest extends FormRequest
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
            'team_id' => 'integer|exists:mysql4.technician_teams,id',
            'employees_nip' => 'required|integer|unique:technicians,karyawan_nip|exists:employees,nip_pgwi',
            'katim' => 'required|integer|in:0,1',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => 'error',
                'errors' => $validator->errors()->all(),
                'input' => $this->input()
            ], 422)
        );
    }
}
