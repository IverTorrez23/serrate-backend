<?php

namespace App\Services;

use App\Models\Orden;
use App\Constants\Estado;
use App\Constants\EtapaOrden;
use App\Constants\TipoUsuario;
use Illuminate\Support\Facades\Auth;

class ContadorOrdenListasRealizarService
{
    //Contador de ordenes listas a realizar
    public function contarOrdenesListasRealizarActivaUsuario(): int
    {
        $usuarioId = Auth::user()->id;
        return Orden::whereNotNull('fecha_recepcion')
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->whereDoesntHave('descarga')
            ->whereHas('causa', function ($query) use ($usuarioId) {
                $query->where('usuario_id', $usuarioId);
            })
            ->whereHas('presupuesto', function ($query) {
                $query->whereNotNull('fecha_entrega');
            })
            ->count();
    }
    public function contarOrdenesListasRealizarActivaAbogadoDependiente(): int
    {
        $usuarioId = Auth::user()->id;
        return Orden::whereNotNull('fecha_recepcion')
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->whereDoesntHave('descarga')
            ->whereHas('causa', function ($query) use ($usuarioId) {
                $query->where('abogado_id', $usuarioId);
            })
            ->whereHas('presupuesto', function ($query) {
                $query->whereNotNull('fecha_entrega');
            })
            ->count();
    }
    public function contarOrdenesListasRealizarActivaProcurador(): int
    {
        $usuarioId = Auth::user()->id;
        return Orden::whereNotNull('fecha_recepcion')
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('procurador_id', $usuarioId)
            ->whereHas('presupuesto', function ($query) {
                $query->whereNotNull('fecha_entrega');
            })
            ->whereDoesntHave('descarga')
            ->count();
    }
    public function contarOrdenesListasRealizarActivaGeneral(): int
    {
        return Orden::whereNotNull('fecha_recepcion')
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->whereHas('presupuesto', function ($query) {
                $query->whereNotNull('fecha_entrega');
            })
            ->whereDoesntHave('descarga')
            ->count();
    }

    public function devuelveCantidadOrdenesListasRealizar()
    {
        $tipoUsuario = Auth::user()->tipo;
        $cantidadListasRealizar = 0;
        if ($tipoUsuario === TipoUsuario::ABOGADO_INDEPENDIENTE || $tipoUsuario === TipoUsuario::ABOGADO_LIDER); {
            $cantidadListasRealizar = $this->contarOrdenesListasRealizarActivaUsuario();
        }
        if ($tipoUsuario === TipoUsuario::ABOGADO_DEPENDIENTE) {
            $cantidadListasRealizar = $this->contarOrdenesListasRealizarActivaAbogadoDependiente();
        }
        if ($tipoUsuario === TipoUsuario::PROCURADOR) {
            $cantidadListasRealizar = $this->contarOrdenesListasRealizarActivaProcurador();
        }
        if ($tipoUsuario === TipoUsuario::ADMINISTRADOR || $tipoUsuario === TipoUsuario::CONTADOR || $tipoUsuario === TipoUsuario::OBSERVADOR || $tipoUsuario === TipoUsuario::PROCURADOR_MAESTRO) {
            $cantidadListasRealizar = $this->contarOrdenesListasRealizarActivaGeneral();
        }
        return $cantidadListasRealizar;
    }
}
