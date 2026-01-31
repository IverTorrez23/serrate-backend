<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJuzgadoRequest extends FormRequest
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
          'nombre_numerico'=>['required'],
          'jerarquia'=>['required','string','max:50'],
          'materia_juzgado'=>['required','string','max:50'],
          'coordenadas'=>['string','max:200'],
          //'foto_url'=>['string','max:200'],
          'foto_url' => ['nullable', 'image'],
          'contacto1'=>['string','max:100'],
          'contacto2'=>['string','max:100'],
          'contacto3'=>['string','max:100'],
          'contacto4'=>['string','max:100'],
          'distrito_id'=>['required'],
          'piso_id'=>['required'],
        ];
    }
}
