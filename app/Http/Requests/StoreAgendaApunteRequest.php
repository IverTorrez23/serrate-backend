<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAgendaApunteRequest extends FormRequest
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
            'detalle_apunte'=>['required','string'],
            'fecha_inicio'=>['required','date_format:Y-m-d H:i:s'],
            'fecha_fin'=>['required','date_format:Y-m-d H:i:s'],
            'color'=>['required','string','max:10'],
            'causa_id'=>['required']
        ];
    }
}
