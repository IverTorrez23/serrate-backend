<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateParticpanteRequest extends FormRequest
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
            'nombres'=>['sometimes','string','max:200'],
            'tipo'=>['sometimes','string','max:20'],
            'foja'=>['sometimes','string','max:20'],
            'ultimo_domicilio'=>['sometimes','string'],
            'causa_id'=>['sometimes'],
        ];
    }
}
