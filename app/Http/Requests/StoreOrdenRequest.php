<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrdenRequest extends FormRequest
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
            'entrega_informacion' => ['required', 'string'],
            'entrega_documentacion' => ['required', 'string'],
            'fecha_inicio' => ['required', 'date_format:Y-m-d H:i'],
            'fecha_fin' => ['required', 'date_format:Y-m-d H:i'],
            'prioridad' => ['required', 'numeric'],
            'lugar_ejecucion' => ['required', 'string', 'max:100'],
            'tiene_propina' => ['required', 'numeric'],
            'propina' => ['required', 'numeric'],
            'causa_id' => ['required'],
            'procurador_id' => ['required'],
        ];
    }
}
