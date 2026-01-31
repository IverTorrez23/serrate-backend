<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAcuerdosUsuariosRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'url_acuerdo_lider' => ['sometimes', 'file', 'mimes:pdf'],
            'url_acuerdo_indep' => ['sometimes', 'file', 'mimes:pdf'],
            'url_acuerdo_proc' => ['sometimes', 'file', 'mimes:pdf'],
        ];
    }
}
