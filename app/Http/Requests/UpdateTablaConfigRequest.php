<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTablaConfigRequest extends FormRequest
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
            'titulo_index' => ['sometimes', 'string'],
            'texto_index' => ['sometimes', 'string'],
            'imagen_index' => ['nullable', 'image'],
            'imagen_logo' => ['nullable', 'image'],
        ];
    }
}
