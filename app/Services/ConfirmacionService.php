<?php
namespace App\Services;

use App\Constants\Estado;
use App\Models\Confirmacion;
use Illuminate\Http\Request;

class ConfirmacionService
{
    public function store($data)
    {
        $confirmacion=Confirmacion::create([
            'confir_sistema'=>$data['confir_sistema'],
            'confir_abogado'=>0,
            'fecha_confir_abogado'=>null,
            'confir_contador'=>0,
            'fecha_confir_contador'=>null,
            'justificacion_rechazo' => '',
            'descarga_id'=>$data['descarga_id'],
            'estado'=>Estado::ACTIVO,
            'es_eliminado'=>0
         ]);
        return $confirmacion;
    }
    public function update($data, $confirmacionId)
    {
        $confirmacion = Confirmacion::findOrFail($confirmacionId);
        $confirmacion->update($data);
        return $confirmacion;
    }

}
