<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVideoRequest extends FormRequest
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
            'link' => ['sometimes', 'string', 'max:150'],
            'titulo' => ['sometimes', 'string', 'max:50'],
            'descripcion' => ['sometimes', 'string', 'max:100'],
            'tipo' => ['sometimes', 'string', 'max:50'],
        ];
    }
}
