<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificacionRequest extends FormRequest
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
            'tipo' => ['sometimes'],
            'evento' => ['sometimes'],
            'emisor' => ['sometimes'],
            'nombre_emisor' => ['sometimes'],
            'tipo_receptor' => ['sometimes'],
            'receptor_estatico' => ['sometimes'],
            'descripcion_receptor_estatico' => ['sometimes'],
            'asunto' => ['sometimes'],
            'envia_notificacion' => ['sometimes'],
            'texto' => ['sometimes']
        ];
    }
}
