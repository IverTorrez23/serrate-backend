<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCotizacionRequest extends FormRequest
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
            'compra'=>['required','numeric'],
            'venta'=>['required','numeric'],
            'penalizacion'=>['required','numeric'],
            'prioridad'=>['required','numeric'],
            'condicion'=>['required','numeric'],
            'orden_id'=>['required','numeric','exists:ordens,id']
        ];
    }
}
