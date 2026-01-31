<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Orden;
use App\Constants\Estado;
use App\Constants\EtapaOrden;
use App\Constants\TipoUsuario;
use Illuminate\Support\Facades\Auth;

class ContadorOrdenVencidasLevesService
{

    //Contador de ordenes vencidas leves (fueron descargadas dentro del plazo, pero aun no estan cerradas y la fecha final ya paso)
    public function contarOrdenesVencidasLevesActivaUsuario(): int
    {
        $now = Carbon::now('America/La_Paz');
        $fechaHora = $now->format('Y-m-d H:i:00');
        $usuarioId = Auth::user()->id;

        return Orden::where('etapa_orden', '!=', EtapaOrden::CERRADA)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('fecha_fin', '<=', $fechaHora)
            ->whereHas('descarga', function ($query) {
                $query->whereHas('confirmacion', function ($query) {
                    $query->where('confir_sistema', 1);
                });
            })
            ->whereHas('causa', function ($query) use ($usuarioId) {
                $query->where('usuario_id', $usuarioId);
            })
            ->count();
    }
    public function contarOrdenesVencidasLevesActivaAbogadoDependiente(): int
    {
        $now = Carbon::now('America/La_Paz');
        $fechaHora = $now->format('Y-m-d H:i:00');
        $usuarioId = Auth::user()->id;

        return Orden::where('etapa_orden', '!=', EtapaOrden::CERRADA)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('fecha_fin', '<=', $fechaHora)
            ->whereHas('descarga', function ($query) {
                $query->whereHas('confirmacion', function ($query) {
                    $query->where('confir_sistema', 1);
                });
            })
            ->whereHas('causa', function ($query) use ($usuarioId) {
                $query->where('abogado_id', $usuarioId);
            })
            ->count();
    }
    public function contarOrdenesVencidasLevesActivaProcurador(): int
    {
        $now = Carbon::now('America/La_Paz');
        $fechaHora = $now->format('Y-m-d H:i:00');
        $usuarioId = Auth::user()->id;

        return Orden::where('etapa_orden', '!=', EtapaOrden::CERRADA)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('procurador_id', $usuarioId)
            ->where('fecha_fin', '<=', $fechaHora)
            ->whereHas('descarga', function ($query) {
                $query->whereHas('confirmacion', function ($query) {
                    $query->where('confir_sistema', 1);
                });
            })
            ->count();
    }
    public function contarOrdenesVencidasLevesActivaGeneral(): int
    {
        $now = Carbon::now('America/La_Paz');
        $fechaHora = $now->format('Y-m-d H:i:00');

        return Orden::where('etapa_orden', '!=', EtapaOrden::CERRADA)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('fecha_fin', '<=', $fechaHora)
            ->whereHas('descarga', function ($query) {
                $query->whereHas('confirmacion', function ($query) {
                    $query->where('confir_sistema', 1);
                });
            })
            ->count();
    }

    public function devuelveCantidadOrdenesVencidasLeves()
    {
        $tipoUsuario = Auth::user()->tipo;
        $cantidadVencidasLeves = 0;
        if ($tipoUsuario === TipoUsuario::ABOGADO_INDEPENDIENTE || $tipoUsuario === TipoUsuario::ABOGADO_LIDER); {
            $cantidadVencidasLeves = $this->contarOrdenesVencidasLevesActivaUsuario();
        }
        if ($tipoUsuario === TipoUsuario::ABOGADO_DEPENDIENTE) {
            $cantidadVencidasLeves = $this->contarOrdenesVencidasLevesActivaAbogadoDependiente();
        }
        if ($tipoUsuario === TipoUsuario::PROCURADOR) {
            $cantidadVencidasLeves = $this->contarOrdenesVencidasLevesActivaProcurador();
        }
        if ($tipoUsuario === TipoUsuario::ADMINISTRADOR || $tipoUsuario === TipoUsuario::CONTADOR || $tipoUsuario === TipoUsuario::OBSERVADOR || $tipoUsuario === TipoUsuario::PROCURADOR_MAESTRO) {
            $cantidadVencidasLeves = $this->contarOrdenesVencidasLevesActivaGeneral();
        }
        return $cantidadVencidasLeves;
    }
}
