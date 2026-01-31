<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGestionAlternativaRequest extends FormRequest
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
            'solicitud_gestion' => ['required', 'string'],
            'orden_id' => ['required', 'exists:ordens,id'],
            'tribunal_id' => ['sometimes'],
            'cuerpo_expediente_id' => ['sometimes'],
        ];
    }
}
