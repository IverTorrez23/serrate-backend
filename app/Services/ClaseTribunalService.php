<?php

namespace App\Services;

use App\Constants\Estado;
use App\Models\ClaseTribunal;
use Illuminate\Http\Request;

class ClaseTribunalService
{
    public function store($data)
    {
        $claseTribunal = ClaseTribunal::create([
            'nombre' => $data['nombre'],
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0
        ]);
        return $claseTribunal;
    }
    public function update($data, $claseTribunalId)
    {
        $claseTribunal = ClaseTribunal::findOrFail($claseTribunalId);
        $claseTribunal->update($data);
        return $claseTribunal;
    }
    public function obtenerUno($claseTribunalId)
    {
        $claseTribunal = ClaseTribunal::findOrFail($claseTribunalId);
        return $claseTribunal;
    }
    public function listarActivos()
    {
        $claseTribunals = ClaseTribunal::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->get();
        return $claseTribunals;
    }
}
