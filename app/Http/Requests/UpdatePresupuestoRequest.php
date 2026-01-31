<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Constants\TipoUsuario;

class UpdatePresupuestoRequest extends FormRequest
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
        //por PATCH
        return [
            'monto'=>['sometimes','numeric'],
            'detalle_presupuesto'=>['sometimes','string'],
            'orden_id'=>['required','exists:ordens,id'],
            //Campos que define el contador para la orden
            'prioridad'=>['required','numeric'],
            'procurador_id'=>['required','numeric'],
          ];
    }
}
