<?php

namespace App\Services;

use App\Models\Orden;
use App\Constants\Estado;
use App\Constants\EtapaOrden;
use App\Constants\TipoUsuario;
use Illuminate\Support\Facades\Auth;

class ContadorOrdenDescargadaService
{
    //Contador de ordenes Descargada
    public function contarOrdenesDescargadaActivaUsuario(): int
    {
        $usuarioId = Auth::user()->id;
        return Orden::where('etapa_orden', EtapaOrden::DESCARGADA)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->whereHas('causa', function ($query) use ($usuarioId) {
                $query->where('usuario_id', $usuarioId);
            })
            ->count();
    }
    public function contarOrdenesDescargadaActivaAbogadoDependiente(): int
    {
        $usuarioId = Auth::user()->id;
        return Orden::where('etapa_orden', EtapaOrden::DESCARGADA)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->whereHas('causa', function ($query) use ($usuarioId) {
                $query->where('abogado_id', $usuarioId);
            })
            ->count();
    }
    public function contarOrdenesDescargadaActivaProcurador(): int
    {
        $usuarioId = Auth::user()->id;
        return Orden::where('etapa_orden', EtapaOrden::DESCARGADA)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('procurador_id', $usuarioId)
            ->count();
    }
    public function contarOrdenesDescargadaActivaGeneral(): int
    {
        return Orden::where('etapa_orden', EtapaOrden::DESCARGADA)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->count();
    }

    public function devuelveCantidadOrdenesDescargadas()
    {
        $tipoUsuario = Auth::user()->tipo;
        $cantidadDescargada = 0;
        if ($tipoUsuario === TipoUsuario::ABOGADO_INDEPENDIENTE || $tipoUsuario === TipoUsuario::ABOGADO_LIDER); {
            $cantidadDescargada = $this->contarOrdenesDescargadaActivaUsuario();
        }
        if ($tipoUsuario === TipoUsuario::ABOGADO_DEPENDIENTE) {
            $cantidadDescargada = $this->contarOrdenesDescargadaActivaAbogadoDependiente();
        }
        if ($tipoUsuario === TipoUsuario::PROCURADOR) {
            $cantidadDescargada = $this->contarOrdenesDescargadaActivaProcurador();
        }
        if ($tipoUsuario === TipoUsuario::ADMINISTRADOR || $tipoUsuario === TipoUsuario::CONTADOR || $tipoUsuario === TipoUsuario::OBSERVADOR || $tipoUsuario === TipoUsuario::PROCURADOR_MAESTRO) {
            $cantidadDescargada = $this->contarOrdenesDescargadaActivaGeneral();
        }
        return $cantidadDescargada;
    }
}
