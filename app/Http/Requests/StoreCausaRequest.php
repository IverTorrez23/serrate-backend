<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCausaRequest extends FormRequest
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
            'observacion'=>['required','string'],
           // 'objetivos'=>['required','string'],

           // 'estrategia'=>['required','string'],
           // 'informacion'=>['required','string'],
            //'apuntes_juridicos'=>['required','string'],
            //'apuntes_honorarios'=>['required','string'],
            'tiene_billetera'=>['required'],

            'color'=>['string','max:10'],
            'materia_id'=>['required'],
            'tipolegal_id'=>['required'],
            'categoria_id'=>['required'],
            //Id de la la tabla avance_plantillas
            'plantilla_id'=>['sometimes'],
            'procurador_id' => ['sometimes'],
            'abogado_id' => ['sometimes']
        ];
    }
}
