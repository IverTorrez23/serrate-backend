<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJuzgadoRequest extends FormRequest
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
          'nombre_numerico'=>['sometimes'],
          'jerarquia'=>['sometimes','string','max:50'],
          'materia_juzgado'=>['sometimes','string','max:50'],
          'coordenadas'=>['sometimes','string','max:200'],
          'foto_url'=>['nullable', 'image'],
          'contacto1'=>['sometimes','string','max:100'],
          'contacto2'=>['sometimes','string','max:100'],
          'contacto3'=>['sometimes','string','max:100'],
          'contacto4'=>['sometimes','string','max:100'],
          'distrito_id'=>['sometimes'],
          'piso_id'=>['sometimes'],
          ];
    }
}
