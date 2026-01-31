<?php

namespace App\Services;

use App\Constants\Estado;
use App\Models\Tribunal;
use Illuminate\Http\Request;

class TribunalService
{
    public function store($data)
    {
        $tribunal = Tribunal::create([
            'expediente' => $data['expediente'],
            'codnurejianuj' => $data['codnurejianuj'],
            'link_carpeta' => $data['link_carpeta'],
            'clasetribunal_id' => $data['clasetribunal_id'],
            'causa_id' => $data['causa_id'],
            'juzgado_id' => $data['juzgado_id'],
            'tribunal_dominante' => $data['tribunal_dominante'],
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,

        ]);
        return $tribunal;
    }
    public function update($data, $tribunalId)
    {
        $tribunal = Tribunal::findOrFail($tribunalId);
        $tribunal->update($data);
        return $tribunal;
    }
    public function obtenerUno($tribunalId)
    {
        $tribunal = Tribunal::findOrFail($tribunalId);
        $tribunal->load('claseTribunal');
        $tribunal->load('juzgado');
        $tribunal->load('juzgado.distrito');
        return $tribunal;
    }
    public function listarActivosPorCausa($causaId)
    {
        $tribunal = Tribunal::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('causa_id', $causaId)
            ->with([
                'claseTribunal',
                'juzgado.distrito',
                'juzgado.piso',
                'cuerpoExpedientes'
            ])
            ->get();
        return $tribunal;
    }
    public function destroy(Tribunal $tribunal)
    {
        $tribunal->es_eliminado = 1;
        $tribunal->save();
        return $tribunal;
    }
    public function listarPorCausaId($causaId)
    {
        $tribunal = Tribunal::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('causa_id', $causaId)
            ->get();
        return $tribunal;
    }

    public function desmarcarTribunaDominanteDeCausa($causaId)
    {
        return Tribunal::where('causa_id', $causaId)
            ->where('tribunal_dominante', 1)
            ->where('es_eliminado', 0)
            ->update(['tribunal_dominante' => 0]);
    }
}
