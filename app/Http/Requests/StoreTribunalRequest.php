<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTribunalRequest extends FormRequest
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
            'expediente'=>['required','string','max:50'],
            'codnurejianuj'=>['required','string','max:50'],
            'clasetribunal_id'=>['required'],
            'causa_id'=>['required'],
            'juzgado_id'=>['required'],
            'tribunal_dominante'=>['required'],
        ];
    }
}
