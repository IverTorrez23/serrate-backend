<?php

namespace App\Services;

use App\Constants\Estado;
use App\Models\SugerenciaOrden;
use Illuminate\Http\Request;

class SugerenciaOrdenService
{
    public function store($data)
    {
        $sugerenciaOrden = SugerenciaOrden::create([
            'orden_id' => $data['orden_id'],
            'sugerencia_gasto_id' => $data['sugerencia_gasto_id'],
            'usuario_id' => $data['usuario_id'],
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0
        ]);
        return $sugerenciaOrden;
    }
    public function update($data, $sugerenciaOrdenId)
    {
        $sugerenciaOrden = SugerenciaOrden::findOrFail($sugerenciaOrdenId);
        $sugerenciaOrden->update($data);
        return $sugerenciaOrden;
    }
    public function destroy(SugerenciaOrden $sugerenciaOrden)
    {
        $sugerenciaOrden->es_eliminado = 1;
        $sugerenciaOrden->save();
        return $sugerenciaOrden;
    }
    public function listarSugerenciasParaUnaOrden($ordenId)
    {
        return SugerenciaOrden::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('orden_id', $ordenId)
            ->get();
    }
    public function eliminarPorOrdenId(int $ordenId): int
    {
        return SugerenciaOrden::where('orden_id', $ordenId)
            ->where('es_eliminado', 0)
            ->update([
                'es_eliminado' => 1
                //'estado' => Estado::INACTIVO
            ]);
    }
}
