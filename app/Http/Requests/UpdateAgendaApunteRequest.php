<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAgendaApunteRequest extends FormRequest
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
        //por PATCH
        return [
            'detalle_apunte'=>['sometimes','string'],
            'fecha_inicio'=>['sometimes','date_format:Y-m-d H:i:s'],
            'fecha_fin'=>['sometimes','date_format:Y-m-d H:i:s'],
            'color'=>['sometimes','string','max:10'],
            'causa_id'=>['sometimes']
          ];
    }
}
