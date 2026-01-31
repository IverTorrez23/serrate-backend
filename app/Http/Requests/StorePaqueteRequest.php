<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaqueteRequest extends FormRequest
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
            'nombre'=>['required','string','max:200'],
            'precio'=>['required','numeric'],
            'cantidad_dias'=>['required','numeric'],
            'descripcion'=>['required','string'],
            'tiene_fecha_limite' => ['required','numeric'],
            'fecha_limite_compra' => ['sometimes','date_format:Y-m-d'],
            'tipo'=>['required','string','max:50'],
        ];
    }
}
