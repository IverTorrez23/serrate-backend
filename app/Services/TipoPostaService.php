<?php

namespace App\Services;

use App\Constants\Estado;
use App\Models\TipoPosta;
use Illuminate\Http\Request;

class TipoPostaService
{
    public function store($data)
    {
        $tipoPosta = TipoPosta::create([
            'nombre' => $data['nombre'],
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0
        ]);
        return $tipoPosta;
    }
    public function update($data, $tipoPostaId)
    {
        $tipoPosta = TipoPosta::findOrFail($tipoPostaId);
        $tipoPosta->update($data);
        return $tipoPosta;
    }
    public function obtenerUno($tipoPostaId)
    {
        $tipoPosta = TipoPosta::findOrFail($tipoPostaId);
        return $tipoPosta;
    }
    public function listarActivos()
    {
        $tipoPosta = TipoPosta::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->get();
        return $tipoPosta;
    }
}
