<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInformePostaRequest extends FormRequest
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
            'foja_informe'=>['sometimes','string','max:20'],
            'fecha_informe'=>['sometimes','date_format:Y-m-d'],
            'honorario_informe'=>['sometimes','string'],

            'foja_truncamiento'=>['sometimes'],
            'fecha_truncamiento'=>['sometimes'],
            'honorario_informe_truncamiento'=>['sometimes'],
            'tipoposta_id'=>['sometimes']
        ];
    }
}
