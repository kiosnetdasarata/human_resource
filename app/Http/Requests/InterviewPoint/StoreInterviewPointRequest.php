<?php

namespace App\Http\Requests\InterviewPoint;

use Illuminate\Foundation\Http\FormRequest;

class StoreInterviewPointRequest extends FormRequest
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
            'presentasi' => 'required|digits_between:1,3',
            'kualitas_kerja' => 'required|digits_between:1,3',
            'etika' => 'required|digits_between:1,3',
            'adaptif' => 'required|digits_between:1,3',
            'kerja_sama' => 'required|digits_between:1,3',
            'disiplin' => 'required|digits_between:1,3',
            'tanggung_jawab' => 'required|digits_between:1,3',
            'inovatif_kreatif' => 'required|digits_between:1,3',
            'problem_solving' => 'required|digits_between:1,3',
            'kemampuan_teknis' => 'required|digits_between:1,3',
            'tugas' => 'required|digits_between:1,3',
            'keterangan_hr' => 'required|string',
            'keterangan_user' => 'required|string',
        ];
    }
}
