<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'current_password' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!Auth::check() || !Hash::check($value, Auth::user()->password)) {
                        $fail('La contraseña actual proporcionada no es válida.');
                    }
                }
            ],
            'new_password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ];
    }

    public function messages()
    {
        return [
            'current_password.required' => 'La contraseña actual es obligatoria.',
            'current_password.string' => 'La contraseña actual debe ser un texto válido.',
            'current_password.invalid' => 'La contraseña actual proporcionada no es válida.',

            'new_password.required' => 'La nueva contraseña es obligatoria.',
            'new_password.string' => 'La nueva contraseña debe ser un texto válido.',
            'new_password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'new_password.confirmed' => 'Las contraseñas no coinciden.',
        ];
    }
}
