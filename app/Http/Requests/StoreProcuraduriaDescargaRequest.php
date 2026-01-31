<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProcuraduriaDescargaRequest extends FormRequest
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
            'detalle_informacion'=>['required','string'],
            'detalle_documentacion'=>['required','string'],
            'ultima_foja'=>['required','string','max:50'],
            'gastos'=>['required','numeric'],

            'detalle_gasto'=>['required','string'],
            'orden_id'=>['required','exists:ordens,id'],
        ];
    }
}
