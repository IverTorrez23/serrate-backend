<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNotificacionRequest extends FormRequest
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
            'tipo' => ['required', 'numeric'],
            'evento' => ['required', 'string'],
            'emisor' => ['sometimes'],
            'nombre_emisor' => ['sometimes'],
            'tipo_receptor' => ['required', 'numeric'],
            'receptor_estatico' => ['sometimes'],
            'descripcion_receptor_estatico' => ['sometimes'],
            'asunto' => ['required', 'string'],
            'envia_notificacion' => ['required', 'numeric'],
            'texto' => ['required', 'string']
        ];
    }
}
