<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTribunalRequest extends FormRequest
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
            'expediente'=>['sometimes','string','max:50'],
            'codnurejianuj'=>['sometimes','string','max:50'],
            'link_carpeta'=>['sometimes'],
            'clasetribunal_id'=>['sometimes'],
            'causa_id'=>['sometimes'],
            'juzgado_id'=>['sometimes'],
            'tribunal_dominante'=>['sometimes'],
          ];
    }
}
