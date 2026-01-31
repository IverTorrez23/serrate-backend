<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInformePostaRequest extends FormRequest
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
            'foja_informe'=>['required','string','max:20'],
            'fecha_informe'=>['required','date_format:Y-m-d'],
           // 'calculo_gasto'=>['required','numeric'],
            'honorario_informe'=>['required','string'],

           // 'foja_truncamiento'=>['required','string','max:20'],
           // 'honorario_informe_truncamiento'=>['required','string'],
            'tipoposta_id'=>['sometimes'],
            'causaposta_id'=>['required']
        ];
    }
}
