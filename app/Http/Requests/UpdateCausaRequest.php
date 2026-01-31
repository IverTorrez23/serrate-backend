<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCausaRequest extends FormRequest
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
        //por PATCH
        return [
            'nombre'=>['sometimes','string','max:200'],
            'observacion'=>['sometimes','string'],
            'color'=>['sometimes','string','max:10'],
            'materia_id'=>['sometimes'],
            'tipolegal_id'=>['sometimes'],
            'categoria_id'=>['sometimes'],
            'abogado_id'=>['sometimes'],
            'procurador_id'=>['sometimes'],
            'plantilla_id'=>['sometimes'],
          ];
    }
}
