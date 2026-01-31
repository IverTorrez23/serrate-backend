<?php

namespace App\Services;

use App\Models\Orden;
use App\Constants\Estado;
use App\Constants\EtapaOrden;
use App\Constants\TipoUsuario;
use Illuminate\Support\Facades\Auth;

class ContadorOrdenDineroEntregadoService
{
    //Contador de ordenes Dinero entregado
    public function contarOrdenesDineroEntregadoActivaUsuario(): int
    {
        $usuarioId = Auth::user()->id;
        return Orden::where('etapa_orden', EtapaOrden::DINERO_ENTREGADO)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->whereNull('fecha_recepcion')
            ->whereHas('causa', function ($query) use ($usuarioId) {
                $query->where('usuario_id', $usuarioId);
            })
            ->count();
    }
    public function contarOrdenesDineroEntregadoActivaAbogadoDependiente(): int
    {
        $usuarioId = Auth::user()->id;
        return Orden::where('etapa_orden', EtapaOrden::DINERO_ENTREGADO)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->whereNull('fecha_recepcion')
            ->whereHas('causa', function ($query) use ($usuarioId) {
                $query->where('abogado_id', $usuarioId);
            })
            ->count();
    }
    public function contarOrdenesDineroEntregadoActivaProcurador(): int
    {
        $usuarioId = Auth::user()->id;
        return Orden::where('etapa_orden', EtapaOrden::DINERO_ENTREGADO)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->whereNull('fecha_recepcion')
            ->where('procurador_id', $usuarioId)
            ->count();
    }
    public function contarOrdenesDineroEntregadoActivaGeneral(): int
    {
        return Orden::where('etapa_orden', EtapaOrden::DINERO_ENTREGADO)
            ->where('estado', Estado::ACTIVO)
            ->whereNull('fecha_recepcion')
            ->where('es_eliminado', 0)
            ->count();
    }

    public function devuelveCantidadOrdenesDineroEntregado()
    {
        $tipoUsuario = Auth::user()->tipo;
        $cantidadDineroEntregado = 0;
        if ($tipoUsuario === TipoUsuario::ABOGADO_INDEPENDIENTE || $tipoUsuario === TipoUsuario::ABOGADO_LIDER); {
            $cantidadDineroEntregado = $this->contarOrdenesDineroEntregadoActivaUsuario();
        }
        if ($tipoUsuario === TipoUsuario::ABOGADO_DEPENDIENTE) {
            $cantidadDineroEntregado = $this->contarOrdenesDineroEntregadoActivaAbogadoDependiente();
        }
        if ($tipoUsuario === TipoUsuario::PROCURADOR) {
            $cantidadDineroEntregado = $this->contarOrdenesDineroEntregadoActivaProcurador();
        }
        if ($tipoUsuario === TipoUsuario::ADMINISTRADOR || $tipoUsuario === TipoUsuario::CONTADOR || $tipoUsuario === TipoUsuario::OBSERVADOR || $tipoUsuario === TipoUsuario::PROCURADOR_MAESTRO) {
            $cantidadDineroEntregado = $this->contarOrdenesDineroEntregadoActivaGeneral();
        }
        return $cantidadDineroEntregado;
    }
}
