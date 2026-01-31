<?php
namespace App\Services;

use App\Constants\Estado;
use App\Models\FinalCosto;
use Illuminate\Http\Request;

class FinalCostoService
{
    public function store($data)
    {
        $finalCosto=FinalCosto::create([
            'costo_procuraduria_compra'=>$data['costo_procuraduria_compra'],
            'costo_procuraduria_venta'=>$data['costo_procuraduria_venta'],
            'costo_procesal_compra'=>$data['costo_procesal_compra'],
            'costo_procesal_venta'=>$data['costo_procesal_venta'],
            'total_egreso'=>$data['total_egreso'],
            'penalidad'=>$data['penalidad'],
            'es_validado'=>$data['es_validado'],
            'cancelado_procurador'=>$data['cancelado_procurador'],
            'ganancia_procuraduria'=>$data['ganancia_procuraduria'],
            'ganancia_procesal'=>$data['ganancia_procesal'],
            'orden_id'=>$data['orden_id'],
            'estado'=>Estado::ACTIVO,
            'es_eliminado'=>0
         ]);
        return $finalCosto;
    }
    public function update($data, $finalCostoId)
    {
        $finalCosto = FinalCosto::findOrFail($finalCostoId);
        $finalCosto->update($data);
        return $finalCosto;
    }

}
