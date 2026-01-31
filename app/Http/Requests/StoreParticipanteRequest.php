<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreParticipanteRequest extends FormRequest
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
            'nombres'=>['required','string','max:200'],
            'tipo'=>['required','string','max:20'],
            'foja'=>['required','string','max:20'],
            'ultimo_domicilio'=>['required','string'],
            'causa_id'=>['required'],
        ];
    }
}
