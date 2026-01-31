<?php

namespace App\Http\Requests\Auth;

use App\Constants\TipoUsuario;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'tipo' => 'required|string|in:' . implode(',', TipoUsuario::getValues()),
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',

            // Opciones moto
            'opciones_moto' => 'nullable|array',
            'opciones_moto.NO_MOTO' => 'nullable|boolean',
            'opciones_moto.SI_MANEJA_NO_TIENE' => 'nullable|boolean',
            'opciones_moto.SI_MOTO' => 'nullable|boolean',
        ];
    }
    public function messages(): array
    {
        return [
            'email.required' => 'El campo email es obligatorio.',
            'email.email' => 'El campo email debe ser una dirección de correo válida.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'password.required' => 'El campo contraseña es obligatorio.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'tipo.required' => 'El campo tipo es obligatorio.',
            'tipo.in' => 'El tipo seleccionado no es válido.',
            'nombre.required' => 'El campo nombre de la persona es obligatorio.',
            'apellido.required' => 'El campo apellido es obligatorio.',
            'telefono.max' => 'El teléfono no debe exceder los 20 caracteres.',
            'direccion.max' => 'La dirección no debe exceder los 255 caracteres.',
            // Opciones moto
            'opciones_moto.array' => 'Las opciones de moto deben ser un arreglo.',
            'opciones_moto.NO_MOTO.boolean' => 'El valor de NO_MOTO debe ser verdadero o falso.',
            'opciones_moto.SI_MANEJA_NO_TIENE.boolean' => 'El valor de SI_MANEJA_NO_TIENE debe ser verdadero o falso.',
            'opciones_moto.SI_MOTO.boolean' => 'El valor de SI_MOTO debe ser verdadero o falso.',
        ];
    }
}
