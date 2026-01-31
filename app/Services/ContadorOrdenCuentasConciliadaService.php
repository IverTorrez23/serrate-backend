<?php

namespace App\Services;

use App\Models\Orden;
use App\Constants\Estado;
use App\Constants\EtapaOrden;
use App\Constants\TipoUsuario;
use Illuminate\Support\Facades\Auth;

class ContadorOrdenCuentasConciliadaService
{
    //Contador de ordenes cuentas conciliadas
    public function contarOrdenesCuentaConciliadaActivaUsuario(): int
    {
        $usuarioId = Auth::user()->id;
        return Orden::where('etapa_orden', EtapaOrden::PRONUNCIO_CONTADOR)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->whereHas('causa', function ($query) use ($usuarioId) {
                $query->where('usuario_id', $usuarioId);
            })
            ->count();
    }
    public function contarOrdenesCuentaConciliadaActivaAbogadoDependiente(): int
    {
        $usuarioId = Auth::user()->id;
        return Orden::where('etapa_orden', EtapaOrden::PRONUNCIO_CONTADOR)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->whereHas('causa', function ($query) use ($usuarioId) {
                $query->where('abogado_id', $usuarioId);
            })
            ->count();
    }
    public function contarOrdenesCuentaConciliadaActivaProcurador(): int
    {
        $usuarioId = Auth::user()->id;
        return Orden::where('etapa_orden', EtapaOrden::PRONUNCIO_CONTADOR)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('procurador_id', $usuarioId)
            ->count();
    }
    public function contarOrdenesCuentaConciliadaActivaGeneral(): int
    {
        return Orden::where('etapa_orden', EtapaOrden::PRONUNCIO_CONTADOR)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->count();
    }

    public function devuelveCantidadOrdenesCuentasConciliadas()
    {
        $tipoUsuario = Auth::user()->tipo;
        $cantidadCuentaConciliada = 0;
        if ($tipoUsuario === TipoUsuario::ABOGADO_INDEPENDIENTE || $tipoUsuario === TipoUsuario::ABOGADO_LIDER); {
            $cantidadCuentaConciliada = $this->contarOrdenesCuentaConciliadaActivaUsuario();
        }
        if ($tipoUsuario === TipoUsuario::ABOGADO_DEPENDIENTE) {
            $cantidadCuentaConciliada = $this->contarOrdenesCuentaConciliadaActivaAbogadoDependiente();
        }
        if ($tipoUsuario === TipoUsuario::PROCURADOR) {
            $cantidadCuentaConciliada = $this->contarOrdenesCuentaConciliadaActivaProcurador();
        }
        if ($tipoUsuario === TipoUsuario::ADMINISTRADOR || $tipoUsuario === TipoUsuario::CONTADOR || $tipoUsuario === TipoUsuario::OBSERVADOR || $tipoUsuario === TipoUsuario::PROCURADOR_MAESTRO) {
            $cantidadCuentaConciliada = $this->contarOrdenesCuentaConciliadaActivaGeneral();
        }
        return $cantidadCuentaConciliada;
    }
}
