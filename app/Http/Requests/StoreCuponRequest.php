<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCuponRequest extends FormRequest
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
            'paquete_id' => [
                'required',
                'integer',
                'exists:paquetes,id',
            ],
            'cantidad' => [
                'required',
                'integer',
                'min:1',
                'max:500',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'paquete_id.required' => 'Debe seleccionar un paquete.',
            'paquete_id.exists' => 'El paquete seleccionado no existe.',
            'cantidad.required' => 'Debe indicar la cantidad de cupones.',
            'cantidad.integer' => 'La cantidad debe ser un número entero.',
            'cantidad.min' => 'Debe generar al menos un cupón.',
            'cantidad.max' => 'Solo puede generar hasta 500 cupones por lote.',
        ];
    }
}
