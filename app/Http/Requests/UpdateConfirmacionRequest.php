<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConfirmacionRequest extends FormRequest
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
        //POR PATCH
        return [
            'confir_abogado'=>['sometimes','numeric'],
            'confir_contador'=>['sometimes','numeric'],
            'justificacion_rechazo'=>['sometimes','nullable','string']
        ];
    }
}
