<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProcuradorPagoRequest extends FormRequest
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
            'procuradorPago.fecha_inicio_consulta' => ['required', 'date_format:Y-m-d H:i'],
            'procuradorPago.fecha_fin_consulta' => ['required', 'date_format:Y-m-d H:i'],
            'procuradorPago.procurador_id' => ['required', 'integer'],
            'procuradorPago.monto' => ['required', 'integer'],

            'finalCosto' => ['required', 'array', 'min:1'],
            'finalCosto.*.id' => ['required', 'integer', 'exists:final_costos,id'],
            'finalCosto.*.orden_id' => ['required', 'integer', 'exists:ordens,id'],
        ];
    }
}
