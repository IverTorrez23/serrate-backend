<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMatrizCotizacionRequest extends FormRequest
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
            'numero_prioridad'=>['sometimes','numeric'],
            'precio_compra'=>['sometimes','numeric'],
            'precio_venta'=>['sometimes','numeric'],
            'penalizacion'=>['sometimes','numeric'],
            'condicion'=>['sometimes','numeric'],
        ];
    }
}
