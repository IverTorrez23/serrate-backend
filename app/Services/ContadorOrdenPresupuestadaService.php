<?php

namespace App\Services;

use App\Models\Orden;
use App\Constants\Estado;
use App\Constants\EtapaOrden;
use App\Constants\TipoUsuario;
use Illuminate\Support\Facades\Auth;

class ContadorOrdenPresupuestadaService
{
    //Contador de ordenes presupuestadas
    public function contarOrdenesPresupuestadaActivaUsuario(): int
    {
        $usuarioId = Auth::user()->id;
        return Orden::where('etapa_orden', EtapaOrden::PRESUPUESTADA)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->whereHas('causa', function ($query) use ($usuarioId) {
                $query->where('usuario_id', $usuarioId);
            })
            ->count();
    }
    public function contarOrdenesPresupuestadaActivaAbogadoDependiente(): int
    {
        $usuarioId = Auth::user()->id;
        return Orden::where('etapa_orden', EtapaOrden::PRESUPUESTADA)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->whereHas('causa', function ($query) use ($usuarioId) {
                $query->where('abogado_id', $usuarioId);
            })
            ->count();
    }
    public function contarOrdenesPresupuestadaActivaProcurador(): int
    {
        $usuarioId = Auth::user()->id;
        return Orden::where('etapa_orden', EtapaOrden::PRESUPUESTADA)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('procurador_id', $usuarioId)
            ->count();
    }
    public function contarOrdenesPresupuestadaActivaGeneral(): int
    {
        return Orden::where('etapa_orden', EtapaOrden::PRESUPUESTADA)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->count();
    }

    public function devuelveCantidadOrdenesPresupuestadas()
    {
        $tipoUsuario = Auth::user()->tipo;
        $cantidadPresupuestada = 0;
        if ($tipoUsuario === TipoUsuario::ABOGADO_INDEPENDIENTE || $tipoUsuario === TipoUsuario::ABOGADO_LIDER); {
            $cantidadPresupuestada = $this->contarOrdenesPresupuestadaActivaUsuario();
        }
        if ($tipoUsuario === TipoUsuario::ABOGADO_DEPENDIENTE) {
            $cantidadPresupuestada = $this->contarOrdenesPresupuestadaActivaAbogadoDependiente();
        }
        if ($tipoUsuario === TipoUsuario::PROCURADOR) {
            $cantidadPresupuestada = $this->contarOrdenesPresupuestadaActivaProcurador();
        }
        if ($tipoUsuario === TipoUsuario::ADMINISTRADOR || $tipoUsuario === TipoUsuario::CONTADOR || $tipoUsuario === TipoUsuario::OBSERVADOR || $tipoUsuario === TipoUsuario::PROCURADOR_MAESTRO) {
            $cantidadPresupuestada = $this->contarOrdenesPresupuestadaActivaGeneral();
        }
        return $cantidadPresupuestada;
    }
}
