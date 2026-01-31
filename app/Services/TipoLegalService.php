<?php

namespace App\Services;

use App\Constants\Estado;
use App\Models\TipoLegal;
use Illuminate\Http\Request;

class TipoLegalService
{
    public function update($data, $tipoLegalId)
    {
        $tipoLegal = TipoLegal::findOrFail($tipoLegalId);
        $tipoLegal->update($data);
        return $tipoLegal;
    }
    public function listarPorMateriaId($materiaId)
    {
        $tipoLegal = TipoLegal::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('materia_id', $materiaId)
            ->orderBy('nombre', 'asc')
            ->get();
        return $tipoLegal;
    }
    public function listarActivos()
    {
        $tipoLegal = TipoLegal::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->get();
        return $tipoLegal;
    }
    public function listarActivosConMateria()
    {
        try {
            $query = TipoLegal::select([
                'id',
                'nombre',
                'abreviatura',
                'materia_id'
            ])->with([
                'materia:id,abreviatura'
            ])->where('estado', Estado::ACTIVO)
                ->where('es_eliminado', 0)
                ->orderBy('nombre', 'asc');

            $result = $query->get();
            return [
                'message' => 'Codigos obtenidas correctamente',
                'data' => $result
            ];
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener las codigos.'], 500);
        }
    }
}
