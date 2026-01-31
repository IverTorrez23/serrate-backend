<?php

namespace App\Services;

use App\Constants\Estado;
use App\Models\GestionAlternativa;
use Illuminate\Http\Request;

class GestionAlternativaService
{
    public function store($data)
    {
        $gestionAlternativa = GestionAlternativa::create([
            'solicitud_gestion' => $data['solicitud_gestion'],
            'fecha_solicitud' => $data['fecha_solicitud'],
            'tribunal_id' => $data['tribunal_id'],
            'cuerpo_expediente_id' => $data['cuerpo_expediente_id'],
            'detalle_gestion' => $data['detalle_gestion'],
            'fecha_respuesta' => $data['fecha_respuesta'],
            'orden_id' => $data['orden_id'],
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0
        ]);
        return $gestionAlternativa;
    }
    public function update($data, $gestionAlternativaId)
    {
        $gestionAlternativa = GestionAlternativa::findOrFail($gestionAlternativaId);
        $gestionAlternativa->update($data);
        return $gestionAlternativa;
    }
    public function destroy(GestionAlternativa $gestionAlternativa)
    {
        $gestionAlternativa->es_eliminado = 1;
        $gestionAlternativa->save();
        return $gestionAlternativa;
    }
    public function obtenerPorOrdenId($ordenId)
    {
        $gestionAlternativas = GestionAlternativa::where('orden_id', $ordenId)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->with([
                'tribunal:id,expediente,clasetribunal_id',
                'tribunal.claseTribunal:id,nombre',
                'cuerpoExpediente:id,nombre,link_cuerpo'
            ])
            ->orderBy('id', 'asc')
            ->get();
        return $gestionAlternativas;
    }
    public function obtenerUnoById($gestionId)
    {
        $gestionAlternativa = GestionAlternativa::where('id', $gestionId)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->with([
                'tribunal:id,expediente,clasetribunal_id',
                'tribunal.claseTribunal:id,nombre',
                'cuerpoExpediente:id,nombre,link_cuerpo'
            ])
            ->first();
        return $gestionAlternativa;
    }
    public function hayGestionesAbiertasDeOrdenes($ordenId): bool
    {
        return GestionAlternativa::where('orden_id', $ordenId)
            ->where('es_eliminado', 0)
            ->where('estado', Estado::ACTIVO)
            ->whereNull('fecha_respuesta')
            ->exists();
    }
    public function contarGestionesPosteriores($gestionId, $ordenId): int
    {
        return GestionAlternativa::where('orden_id', $ordenId)
            ->where('id', '>', $gestionId)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->count();
    }
}
