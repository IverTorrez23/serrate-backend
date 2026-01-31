<?php

namespace App\Http\Requests\Usuarios;

use App\Constants\TipoUsuario;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRegisterRequest extends FormRequest
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
            // Campos del usuario (opcionalmente validados si se envían)
            'email' => 'nullable|string|email|max:255|unique:users,email',
            'password' => 'nullable|string|min:8',
            'tipo' => 'nullable|string|in:' . implode(',', TipoUsuario::getValues()),
            'name' => 'nullable|string|max:255',
            'abogado_id' => 'nullable|exists:users,id',

            // Opciones de moto
            'opciones_moto' => 'nullable|array',
            'opciones_moto.NO_MOTO' => 'nullable|boolean',
            'opciones_moto.SI_MANEJA_NO_TIENE' => 'nullable|boolean',
            'opciones_moto.SI_MOTO' => 'nullable|boolean',

            // Persona anidada
            'persona.nombre' => 'nullable|string|max:255',
            'persona.apellido' => 'nullable|string|max:255',
            'persona.telefono' => 'nullable|string|max:20',
            'persona.direccion' => 'nullable|string|max:255',
            'persona.coordenadas' => 'nullable|string|max:255',
            'persona.observacion' => 'nullable|string|max:500',
            'persona.foto_url' => 'nullable|url',
        ];
    }

    public function messages(): array
    {
        return [
            // Email
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.max' => 'El correo electrónico no debe exceder los 255 caracteres.',
            'email.unique' => 'Este correo ya está registrado.',

            // Password
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',

            // Tipo
            'tipo.in' => 'El tipo seleccionado no es válido.',

            // Nombre
            'name.max' => 'El nombre no debe exceder los 255 caracteres.',

            // Abogado ID
            'abogado_id.exists' => 'El abogado seleccionado no existe.',

            // Opciones moto
            'opciones_moto.array' => 'Las opciones de moto deben ser un arreglo.',
            'opciones_moto.NO_MOTO.boolean' => 'El valor de NO_MOTO debe ser verdadero o falso.',
            'opciones_moto.SI_MANEJA_NO_TIENE.boolean' => 'El valor de SI_MANEJA_NO_TIENE debe ser verdadero o falso.',
            'opciones_moto.SI_MOTO.boolean' => 'El valor de SI_MOTO debe ser verdadero o falso.',

            // Persona
            'persona.nombre.max' => 'El nombre no debe exceder los 255 caracteres.',
            'persona.apellido.max' => 'El apellido no debe exceder los 255 caracteres.',
            'persona.telefono.max' => 'El teléfono no debe exceder los 20 caracteres.',
            'persona.direccion.max' => 'La dirección no debe exceder los 255 caracteres.',
            'persona.coordenadas.max' => 'Las coordenadas no deben exceder los 255 caracteres.',
            'persona.observacion.max' => 'La observación no debe exceder los 500 caracteres.',
            'persona.foto_url.url' => 'La URL de la foto debe ser válida.',
        ];
    }
}
