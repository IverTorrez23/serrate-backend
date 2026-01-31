<?php

namespace App\Http\Requests;

use App\Constants\TipoUsuario;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StorePresupuestoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (Auth::user()->tipo === TipoUsuario::CONTADOR) {
            return true;
        } else {
            return false;
        }
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
            'detalle_presupuesto' => ['required', 'string'],
            'orden_id' => ['required', 'exists:ordens,id'],
            //Campos que define el contador para la orden
            'prioridad' => ['required', 'numeric'],
            'procurador_id' => ['required', 'numeric'],
        ];
    }
}
