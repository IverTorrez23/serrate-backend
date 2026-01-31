<?php
namespace App\Services;

use App\Models\Cotizacion;
use App\Constants\Estado;
use Illuminate\Http\Request;

class CotizacionService
{
    public function store($data)
    {
        $cotizacion=Cotizacion::create([
            'compra'=>$data['compra'],
            'venta'=>$data['venta'],
            'penalizacion'=>$data['penalizacion'],
            'prioridad'=>$data['prioridad'],
            'condicion'=>$data['condicion'],
            'orden_id'=>$data['orden_id'],
            'estado'=>Estado::ACTIVO,
            'es_eliminado'=>0
         ]);
        return $cotizacion;
    }
    public function update($data, $cotizacionId)
    {
        $cotizacion = Cotizacion::findOrFail($cotizacionId);
        $cotizacion->update($data);
        return $cotizacion;
    }
    public function obtenerUno($cotizacionId){
        $cotizacion = Cotizacion::findOrFail($cotizacionId);
        return $cotizacion;
    }
    public function obtenerPorIdOrden($ordenId){
        $cotizacion = Cotizacion::where('orden_id', $ordenId)
                                ->first();
        return $cotizacion;
    }
}
