<?php

namespace App\Services;

use App\Constants\Estado;
use App\Models\CuerpoExpediente;
use Illuminate\Http\Request;

class CuerpoExpedienteService
{
    public function store($data)
    {
        $cuerpoExpediente = CuerpoExpediente::create([
            'nombre' => $data['nombre'],
            'link_cuerpo' => $data['link_cuerpo'],
            'tribunal_id' => $data['tribunal_id'],
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0
        ]);
        return $cuerpoExpediente;
    }
    public function update($data, $cuerpoExpedienteId)
    {
        $cuerpoExpediente = CuerpoExpediente::findOrFail($cuerpoExpedienteId);
        $cuerpoExpediente->update($data);
        return $cuerpoExpediente;
    }
    public function obtenerUno($cuerpoExpedienteId)
    {
        $cuerpoExpediente = CuerpoExpediente::findOrFail($cuerpoExpedienteId);
        return $cuerpoExpediente;
    }
    public function listarActivos()
    {
        $cuerpoExpedientes = CuerpoExpediente::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->get();
        return $cuerpoExpedientes;
    }
    public function destroy($cuerpoExpedienteId)
    {
        $cuerpoExpediente = CuerpoExpediente::findOrFail($cuerpoExpedienteId);
        $cuerpoExpediente->es_eliminado   = 1;
        $cuerpoExpediente->save();
        return $cuerpoExpediente;
    }
    public function listarExpedientesDigitalDeTribunal($tribunalId)
    {
        $cuerpoExpedientes = CuerpoExpediente::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('tribunal_id', $tribunalId)
            ->get();
        return $cuerpoExpedientes;
    }
}
