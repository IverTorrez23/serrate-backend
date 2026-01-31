<?php

namespace App\Services;

use App\Constants\Estado;
use App\Models\Participante;
use Illuminate\Http\Request;

class ParticipanteService
{
    public function store($data)
    {
        $participante = Participante::create([
            'nombres' => $data['nombres'],
            'tipo' => $data['tipo'],
            'foja' => $data['foja'],
            'ultimo_domicilio' => $data['ultimo_domicilio'],
            'causa_id' => $data['causa_id'],
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0
        ]);
        return $participante;
    }
    public function update($data, $participanteId)
    {
        $participante = Participante::findOrFail($participanteId);
        $participante->update($data);
        return $participante;
    }
    public function obtenerUno($participanteId)
    {
        $participante = Participante::findOrFail($participanteId);
        return $participante;
    }
    public function listarActivos()
    {
        $participantes = Participante::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->get();
        return $participantes;
    }
    public function destroy($participanteId)
    {
        $participante = Participante::findOrFail($participanteId);
        $participante->es_eliminado   = 1;
        $participante->save();
        return $participante;
    }
    public function listarPorCausaId($causaId)
    {
        $participantes = Participante::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('causa_id', $causaId)
            ->orderBy('id', 'asc')
            ->get();
        return $participantes;
    }
}
