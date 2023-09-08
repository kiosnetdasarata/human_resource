<?php

namespace App\Http\Requests\Technician;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTechnicianRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->isMethod('patch');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'team_id' => 'integer|exists:developer_dasarata_operasional.technician_teams,id',
            'employees_nip' => 'integer|unique:technicians,karyawan_nip|exists:employees,nip_pgwi',
            'katim' => 'integer|in:0,1',
        ];
    }
}
