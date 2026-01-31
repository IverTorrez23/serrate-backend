<?php

namespace App\Http\Requests;

use App\Constants\TipoUsuario;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreCompraPaqueteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->tipo === TipoUsuario::ABOGADO_INDEPENDIENTE || Auth::user()->tipo === TipoUsuario::ABOGADO_LIDER;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'monto' => ['required', 'numeric'],
            'paquete_id' => ['required', 'numeric', 'exists:paquetes,id']
        ];
    }
}
