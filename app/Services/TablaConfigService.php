<?php

namespace App\Services;

use App\Constants\Estado;
use App\Constants\TipoTransaccion;
use App\Models\TablaConfig;
use Illuminate\Http\Request;

class TablaConfigService
{
    public function update($data, $tablaConfigId)
    {
        $tablaConfig = TablaConfig::findOrFail($tablaConfigId);
        $tablaConfig->update($data);
        return $tablaConfig;
    }
    public function mostarDatosTablaConfig()
    {
        $tablaConfig = TablaConfig::findOrFail(1);
        return $tablaConfig;
    }
    public function actualizarCajaAdimin($tipoTrn, $monto)
    {
        $tablaConfig = TablaConfig::findOrFail(1);
        $nuevoSaldoCaja = 0;
        if ($tipoTrn === TipoTransaccion::CREDITO) {
            $nuevoSaldoCaja = $tablaConfig->caja_admin + $monto;
        } elseif ($tipoTrn === TipoTransaccion::DEBITO) {
            $nuevoSaldoCaja = $tablaConfig->caja_admin - $monto;
        }
        $dataSaldo = [
            'caja_admin' => $nuevoSaldoCaja
        ];
        $tablaConfig->update($dataSaldo);
        return $tablaConfig;
    }
    public function actualizarCajaContador($tipoTrn, $monto)
    {
        $tablaConfig = TablaConfig::findOrFail(1);
        $nuevoSaldoCaja = 0;
        if ($tipoTrn === TipoTransaccion::CREDITO) {
            $nuevoSaldoCaja = $tablaConfig->caja_contador + $monto;
        } elseif ($tipoTrn === TipoTransaccion::DEBITO) {
            $nuevoSaldoCaja = $tablaConfig->caja_contador - $monto;
        }
        $dataSaldo = [
            'caja_contador' => $nuevoSaldoCaja
        ];
        $tablaConfig->update($dataSaldo);
        return $tablaConfig;
    }
    public function obtenerDatos()
    {
        $tablaConfig = TablaConfig::findOrFail(1);
        return $tablaConfig;
    }
    public function obtenerArancelAbogados()
    {
        return TablaConfig::select('id', 'nombre', 'archivo_url')
            ->where('id', 1)
            ->first(); 
    }
}
