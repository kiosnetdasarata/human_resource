<?php

namespace App\Http\Requests\EmployeeHistory;

use App\Http\Requests\Sales\StoreSalesRequest;
use App\Http\Requests\Technician\StoreTechnicianRequest;
use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeHistoryRequest extends FormRequest
{
    public function __construct(
        private StoreTechnicianRequest $storeTechnicianRequest,
        private StoreSalesRequest $storeSalesRequest,
    ){}
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
        $rules = collect([
            'pgwi_nip' => 'required|integer|unique:employee_history,pgwi_nip',
            'tgl_berakhir' => 'required|date_format:Y-m-d',
            'keterangan' => 'required|string|in,Habis Kontrak,PHK,Pindah Divisi',
        ]);

        if ($this->input('keterangan') === 'Pindah Divisi') {
            $rules->merge([
                'after_divisi_id' => 'required|integer|exists:divisions,id',
                'after_job_title_id' => 'required|integer|exists:job_titles,id'
            ]);

            if ($this->input('after_divisi_id') == 4) {
                $rules->merge($this->storeSalesRequest->rules());
            } else if ($this->input('after_divisi_id') == 5) {
                $rules->merge($this->storeTechnicianRequest->rules());
            }
        }

        return $rules->toArray();
    }
}
