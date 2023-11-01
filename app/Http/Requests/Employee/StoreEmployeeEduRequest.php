<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeEduRequest extends FormRequest
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
            'pendidikan_terakhir' => 'required|in:Sarjana,SMK/SMA,SMP',
            'nama_instansi' => 'required|string',
            'tahun_lulus' => 'required|digits:4',
        ];
    }
}
