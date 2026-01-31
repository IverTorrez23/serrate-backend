<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrdenRequest extends FormRequest
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
            'entrega_informacion'=>['sometimes','string'],
            'entrega_documentacion'=>['sometimes','string'],
            'fecha_inicio'=>['sometimes','date_format:Y-m-d H:i'],
            'fecha_fin'=>['sometimes','date_format:Y-m-d H:i'],
            'prioridad'=>['sometimes','numeric'],
            'lugar_ejecucion'=>['sometimes','string','max:100'],
            'tiene_propina'=>['sometimes','numeric'],
            'propina'=>['sometimes','numeric'],
            'procurador_id'=>['sometimes'],
          //  'sugerencia_presupuesto'=>['sometimes','string'],
        ];
    }
}
