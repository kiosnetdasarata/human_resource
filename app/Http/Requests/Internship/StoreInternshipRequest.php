<?php

namespace App\Http\Requests\Internship;

use Illuminate\Validation\Rules\File;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreInternshipRequest extends FormRequest
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
            'traineeship_id' => 'required|exists:traineeships,id',
            'nama_lengkap' => 'required|string',
            'alamat' => 'required|string',
            'jk' => 'required|in:Laki-Laki,Perempuan',
            'email' => 'required|email|unique:internships,email',
            'no_tlpn' => 'required|min:10|max:|unique:internships,no_telp',
            'role_id' => 'required|in:role,id',
            'supervisor' => 'required|in:employee,id',
            'tgl_masuk' => 'required|date_format:Y-m-d',
            'file_cv' => ['required', File::types(['pdf'])->max(5 * 1024),],
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
