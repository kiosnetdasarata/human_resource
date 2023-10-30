<?php

namespace App\Http\Requests\Internship;

use App\Rules\SocialMediaLink;
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
            'nama_lengkap' => 'string',
            'no_tlpn' => 'min:10|max:15|unique:internships,no_telp'.$internship.',id',
            'email' => 'email|unique:internships,email,'.$internship.',id',
            'alamat' => 'string',
            'link_sosmed' => ['url', new SocialMediaLink],
            'is_kuliah' => 'in:0,1',
            'nama_instansi' => 'string',
            'semester' => 'required_if:is_kuliah,1|numeric|max:20',
            'tahun_lulus' => 'required_if:is_kuliah,0',
            'mitra_id' => 'in:partnerships,id',
            'role_id' => 'exists:roles,id',
            'status_internship' => 'in:Internship,Magang',
            'status_phase' => 'in:Onboarding,Join,Selesai',
            'supervisor' => 'exists:employee_personal_informations,nip',
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