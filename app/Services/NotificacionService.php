<?php

namespace App\Services;

use App\Constants\Estado;
use App\Models\Notificacion;
use Illuminate\Http\Request;

class NotificacionService
{
    public function store($data)
    {
        $notificacion = Notificacion::create([
            'tipo' => $data['tipo'],
            'evento' => $data['evento'],
            'emisor' => $data['emisor'],
            'nombre_emisor' => $data['nombre_emisor'],
            'tipo_receptor' => $data['tipo_receptor'],
            'receptor_estatico' => $data['receptor_estatico'],
            'descripcion_receptor_estatico' => $data['descripcion_receptor_estatico'],
            'asunto' => $data['asunto'],
            'envia_notificacion' => $data['envia_notificacion'],
            'texto' => $data['texto'],
            'usuario_id' => $data['usuario_id'],
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0
        ]);
        return $notificacion;
    }
    public function update($data, $notificacionId)
    {
        $notificacion = Notificacion::findOrFail($notificacionId);
        $notificacion->update($data);
        return $notificacion;
    }
    public function obtenerUno($notificacionId)
    {
        $notificacion = Notificacion::findOrFail($notificacionId);
        return $notificacion;
    }
    public function listarActivos()
    {
        $notificaciones = Notificacion::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->get();
        return $notificaciones;
    }
    public function destroy(Notificacion $notificacion)
    {
        $notificacion->es_eliminado = 1;
        $notificacion->save();
        return $notificacion;
    }
}
