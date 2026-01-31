<?php

namespace App\Services;

use App\Constants\Estado;
use App\Models\Presupuesto;
use Illuminate\Http\Request;

class PresupuestoService
{
    public function store($data)
    {
        $presupuesto = Presupuesto::create([
            'monto' => $data['monto'],
            'detalle_presupuesto' => $data['detalle_presupuesto'],
            'fecha_presupuesto' => $data['fecha_presupuesto'],
            'fecha_entrega' => $data['fecha_entrega'],
            'contador_id' => $data['contador_id'],
            'orden_id' => $data['orden_id'],
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0
        ]);
        return $presupuesto;
    }
    public function update($data, $presupuestoId)
    {
        $presupuesto = Presupuesto::findOrFail($presupuestoId);
        $presupuesto->update($data);
        return $presupuesto;
    }
    public function destroy(Presupuesto $presupuesto)
    {
        $presupuesto->es_eliminado = 1;
        $presupuesto->save();
        return $presupuesto;
    }
    public function obtenerUnoPorOrdenId($ordenId)
    {
        $presupuesto = Presupuesto::where('orden_id', $ordenId)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->first();
        return $presupuesto;
    }
    public function tienePresupuestoActivo($ordenId): bool
    {
        return Presupuesto::where('orden_id', $ordenId)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->exists();
    }
}
