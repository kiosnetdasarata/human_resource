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
        // dd('aaa');
        return [
            'status_internship' => 'required|in:Internship,Magang',
            'status_phase' => 'required|in:Onboarding,Join,Selesai',
            'tanggal_masuk' => 'required|date:Y-m-d',
            'mitra_id' => 'int|exists:partnerships,id',
            // 'supervisor' => 'in:employee,id',
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
