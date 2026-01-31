<?php

namespace App\Http\Resources\Usuario;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsuarioResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'tipo' => $this->tipo,
            'abogado_id' => $this->abogado_id,
            'opciones_moto' => $this->opciones_moto,
            'estado' => $this->estado,
            'es_eliminado' => $this->es_eliminado,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'persona' => $this->persona ? [
                'id' => $this->persona->id,
                'nombre' => $this->persona->nombre,
                'apellido' => $this->persona->apellido,
                'telefono' => $this->persona->telefono,
                'direccion' => $this->persona->direccion,
                'coordenadas' => $this->persona->coordenadas,
                'observacion' => $this->persona->observacion,
                'foto_url' => $this->persona->foto_url,
                'estado' => $this->persona->estado,
                'es_eliminado' => $this->persona->es_eliminado,
                'created_at' => $this->persona->created_at,
                'updated_at' => $this->persona->updated_at,
            ] : null,
        ];
        // return parent::toArray($request);
    }
}
