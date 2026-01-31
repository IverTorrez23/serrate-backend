<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Orden;
use App\Constants\Estado;
use App\Constants\EtapaOrden;
use App\Constants\TipoUsuario;
use Illuminate\Support\Facades\Auth;

class ContadorOrdenVencidasGravesService
{

    //Contador de ordenes vencidas graves (aun no fueron descargadas y el plazo se vencio)
    public function contarOrdenesVencidasGravesActivaUsuario(): int
    {
        $now = Carbon::now('America/La_Paz');
        $fechaHora = $now->format('Y-m-d H:i:00');
        $usuarioId = Auth::user()->id;

        return Orden::where('etapa_orden', '!=', EtapaOrden::CERRADA)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('fecha_fin', '<', $fechaHora)
            ->whereDoesntHave('descarga') //Verifica que no exista registros en descarga
            ->whereHas('causa', function ($query) use ($usuarioId) {
                $query->where('usuario_id', $usuarioId);
            })
            ->count();
    }
    public function contarOrdenesVencidasGravesActivaAbogadoDependiente(): int
    {
        $now = Carbon::now('America/La_Paz');
        $fechaHora = $now->format('Y-m-d H:i:00');
        $usuarioId = Auth::user()->id;

        return Orden::where('etapa_orden', '!=', EtapaOrden::CERRADA)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('fecha_fin', '<', $fechaHora)
            ->whereDoesntHave('descarga') //Verifica que no exista registros en descarga
            ->whereHas('causa', function ($query) use ($usuarioId) {
                $query->where('abogado_id', $usuarioId);
            })
            ->count();
    }
    public function contarOrdenesVencidasGravesActivaProcurador(): int
    {
        $now = Carbon::now('America/La_Paz');
        $fechaHora = $now->format('Y-m-d H:i:00');
        $usuarioId = Auth::user()->id;

        return Orden::where('etapa_orden', '!=', EtapaOrden::CERRADA)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('procurador_id', $usuarioId)
            ->where('fecha_fin', '<', $fechaHora)
            ->whereDoesntHave('descarga') //Verifica que no exista registros en descarga
            ->count();
    }
    public function contarOrdenesVencidasGravesActivaGeneral(): int
    {
        $now = Carbon::now('America/La_Paz');
        $fechaHora = $now->format('Y-m-d H:i:00');

        return Orden::where('etapa_orden', '!=', EtapaOrden::CERRADA)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('fecha_fin', '<', $fechaHora)
            ->whereDoesntHave('descarga') //Verifica que no exista registros en descarga
            ->count();
    }

    public function devuelveCantidadOrdenesVencidasGraves()
    {
        $tipoUsuario = Auth::user()->tipo;
        $cantidadVencidasGraves = 0;
        if ($tipoUsuario === TipoUsuario::ABOGADO_INDEPENDIENTE || $tipoUsuario === TipoUsuario::ABOGADO_LIDER); {
            $cantidadVencidasGraves = $this->contarOrdenesVencidasGravesActivaUsuario();
        }
        if ($tipoUsuario === TipoUsuario::ABOGADO_DEPENDIENTE) {
            $cantidadVencidasGraves = $this->contarOrdenesVencidasGravesActivaAbogadoDependiente();
        }
        if ($tipoUsuario === TipoUsuario::PROCURADOR) {
            $cantidadVencidasGraves = $this->contarOrdenesVencidasGravesActivaProcurador();
        }
        if ($tipoUsuario === TipoUsuario::ADMINISTRADOR || $tipoUsuario === TipoUsuario::CONTADOR || $tipoUsuario === TipoUsuario::OBSERVADOR || $tipoUsuario === TipoUsuario::PROCURADOR_MAESTRO) {
            $cantidadVencidasGraves = $this->contarOrdenesVencidasGravesActivaGeneral();
        }
        return $cantidadVencidasGraves;
    }
}
