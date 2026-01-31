<?php

namespace App\Services;

use App\Constants\Estado;
use App\Models\RegistroLlamada;
use Illuminate\Http\Request;

class RegistroLlamadaService
{
    public function store($data)
    {
        $registroLlamada = RegistroLlamada::create([
            'numero_telefono' => $data['numero_telefono'],
            'fecha_llamada' => $data['fecha_llamada'],
            'gestion_id' => $data['gestion_id'],
            'usuario_id' => $data['usuario_id'],
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0
        ]);
        return $registroLlamada;
    }
    public function update($data, $registroLlamadaId)
    {
        $registroLlamada = RegistroLlamada::findOrFail($registroLlamadaId);
        $registroLlamada->update($data);
        return $registroLlamada;
    }
    public function destroy(RegistroLlamada $registroLlamada)
    {
        $registroLlamada->es_eliminado = 1;
        $registroLlamada->save();
        return $registroLlamada;
    }
    public function obtenerPorGestionId($gestionId)
    {
        $registroLlamadas = RegistroLlamada::where('gestion_id', $gestionId)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->orderBy('id', 'asc')
            ->get();
        return $registroLlamadas;
    }
    public function obtenerUnoById($registroLlamadaId)
    {
        $registroLlamada = RegistroLlamada::where('id', $registroLlamadaId)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->first();
        return $registroLlamada;
    }
}
