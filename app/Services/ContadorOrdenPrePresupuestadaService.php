<?php

namespace App\Services;

use App\Models\Orden;
use App\Constants\Estado;
use App\Constants\EtapaOrden;
use App\Constants\TipoUsuario;
use Illuminate\Support\Facades\Auth;

class ContadorOrdenPrePresupuestadaService
{
    //Funciones para Contadores de ordenes PRE-PRESUPUESTADAS
    public function contarOrdenesPrePresupuestadasActivaUsuario(): int
    {
        $usuarioId = Auth::user()->id;
        return Orden::where('etapa_orden', EtapaOrden::PREPRESUPUESTADA)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->whereHas('causa', function ($query) use ($usuarioId) {
                $query->where('usuario_id', $usuarioId);
            })
            ->count();
    }
    public function contarOrdenesPrePresupuestadasActivaAbogadoDependiente(): int
    {
        $usuarioId = Auth::user()->id;
        return Orden::where('etapa_orden', EtapaOrden::PREPRESUPUESTADA)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->whereHas('causa', function ($query) use ($usuarioId) {
                $query->where('abogado_id', $usuarioId);
            })
            ->count();
    }
    public function contarOrdenesPrePresupuestadaActivaProcurador(): int
    {
        $usuarioId = Auth::user()->id;
        return Orden::where('etapa_orden', EtapaOrden::PREPRESUPUESTADA)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('procurador_id', $usuarioId)
            ->count();
    }
    public function contarOrdenesPrePresupuestadaActivaGeneral(): int
    {
        return Orden::where('etapa_orden', EtapaOrden::PREPRESUPUESTADA)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->count();
    }

    public function devuelveCantidadOrdenesPrePresupuestadas()
    {
        $tipoUsuario = Auth::user()->tipo;
        $cantidadPrePresupuestada = 0;
        if ($tipoUsuario === TipoUsuario::ABOGADO_INDEPENDIENTE || $tipoUsuario === TipoUsuario::ABOGADO_LIDER); {
            $cantidadPrePresupuestada = $this->contarOrdenesPrePresupuestadasActivaUsuario();
        }
        if ($tipoUsuario === TipoUsuario::ABOGADO_DEPENDIENTE) {
            $cantidadPrePresupuestada = $this->contarOrdenesPrePresupuestadasActivaAbogadoDependiente();
        }
        if ($tipoUsuario === TipoUsuario::PROCURADOR) {
            $cantidadPrePresupuestada = $this->contarOrdenesPrePresupuestadaActivaProcurador();
        }
        if ($tipoUsuario === TipoUsuario::ADMINISTRADOR || $tipoUsuario === TipoUsuario::CONTADOR || $tipoUsuario === TipoUsuario::OBSERVADOR || $tipoUsuario === TipoUsuario::PROCURADOR_MAESTRO) {
            $cantidadPrePresupuestada = $this->contarOrdenesPrePresupuestadaActivaGeneral();
        }
        return $cantidadPrePresupuestada;
    }
}
