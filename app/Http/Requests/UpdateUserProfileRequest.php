<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . auth()->id(),
            'persona.nombre' => 'sometimes|string|max:255',
            'persona.apellido' => 'sometimes|string|max:255',
            'persona.telefono' => 'sometimes|string|max:15',
            'persona.direccion' => 'sometimes|string|max:255',
            'persona.coordenadas' => 'sometimes|string|max:255',
            'persona.observacion' => 'sometimes|string|max:255',

        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'El nombre debe ser texto.',
            'name.max' => 'Máximo 255 caracteres en el nombre.',

            'email.email' => 'Correo inválido.',
            'email.unique' => 'Correo ya registrado.',

            'persona.nombre.string' => 'El nombre debe ser texto.',
            'persona.nombre.max' => 'Máximo 255 caracteres en el nombre.',

            'persona.apellido.string' => 'El apellido debe ser texto.',
            'persona.apellido.max' => 'Máximo 255 caracteres en el apellido.',

            'persona.telefono.string' => 'El teléfono debe ser texto.',
            'persona.telefono.max' => 'Máximo 15 caracteres en el teléfono.',

            'persona.direccion.string' => 'La dirección debe ser texto.',
            'persona.direccion.max' => 'Máximo 255 caracteres en la dirección.',
            'persona.coordenadas.string' => 'Debe ingresar coordenadas',
            'persona.coordenadas.max' => 'Máximo 255 caracteres en la dirección.',

            'persona.observacion.string' => 'La observación debe ser texto.',
            'persona.observacion.max' => 'Máximo 255 caracteres en la observación.',

        ];
    }
}
