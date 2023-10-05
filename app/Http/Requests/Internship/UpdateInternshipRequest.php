<?php

namespace App\Http\Requests\Internship;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateInternshipRequest extends FormRequest
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
        $internship = $this->route('internship');
        return [
            'traineeship_id' => 'exists:traineeships,id',
            'nama_lengkap' => 'string',
            'alamat' => 'string',
            'jk' => 'in:Laki-Laki,Perempuan',
            'email' => 'email|unique:internship,email'.$internship.',uuid',
            'no_tlpn' => 'min:10|max:15|unique:internship,no_telp'.$internship.',uuid',
            'role_id' => 'in:role,id',
            'supervisor' => 'in:employee,id',
            'tgl_masuk' => 'date_format:Y-m-d',
            'file_cv' => 'file_type:pdf|file_size:5000',
            'mitra_id' => 'in:parnertships,id',
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