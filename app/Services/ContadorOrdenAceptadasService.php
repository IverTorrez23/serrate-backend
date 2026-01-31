<?php

namespace App\Services;

use App\Models\Orden;
use App\Constants\Estado;
use App\Constants\EtapaOrden;
use App\Constants\TipoUsuario;
use Illuminate\Support\Facades\Auth;

class ContadorOrdenAceptadasService
{
    //Contador de ordenes aceptdas
    public function contarOrdenesAceptadasActivaUsuario(): int
    {
        $usuarioId = Auth::user()->id;
        return Orden::where('etapa_orden', EtapaOrden::ACEPTADA)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->whereHas('causa', function ($query) use ($usuarioId) {
                $query->where('usuario_id', $usuarioId);
            })
            ->whereHas('presupuesto', function ($query) {
                $query->whereNull('fecha_entrega');
            })
            ->count();
    }
    public function contarOrdenesAceptadasActivaAbogadoDependiente(): int
    {
        $usuarioId = Auth::user()->id;
        return Orden::where('etapa_orden', EtapaOrden::ACEPTADA)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->whereHas('causa', function ($query) use ($usuarioId) {
                $query->where('abogado_id', $usuarioId);
            })
            ->whereHas('presupuesto', function ($query) {
                $query->whereNull('fecha_entrega');
            })
            ->count();
    }
    public function contarOrdenesAceptadasActivaProcurador(): int
    {
        $usuarioId = Auth::user()->id;
        return Orden::where('etapa_orden', EtapaOrden::ACEPTADA)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('procurador_id', $usuarioId)
            ->whereHas('presupuesto', function ($query) {
                $query->whereNull('fecha_entrega');
            })
            ->count();
    }
    public function contarOrdenesAceptadasActivaGeneral(): int
    {
        return Orden::where('etapa_orden', EtapaOrden::ACEPTADA)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->whereHas('presupuesto', function ($query) {
                $query->whereNull('fecha_entrega');
            })
            ->count();
    }

    public function devuelveCantidadOrdenesAceptadas()
    {
        $tipoUsuario = Auth::user()->tipo;
        $cantidadAceptadas = 0;
        if ($tipoUsuario === TipoUsuario::ABOGADO_INDEPENDIENTE || $tipoUsuario === TipoUsuario::ABOGADO_LIDER); {
            $cantidadAceptadas = $this->contarOrdenesAceptadasActivaUsuario();
        }
        if ($tipoUsuario === TipoUsuario::ABOGADO_DEPENDIENTE) {
            $cantidadAceptadas = $this->contarOrdenesAceptadasActivaAbogadoDependiente();
        }
        if ($tipoUsuario === TipoUsuario::PROCURADOR) {
            $cantidadAceptadas = $this->contarOrdenesAceptadasActivaProcurador();
        }
        if ($tipoUsuario === TipoUsuario::ADMINISTRADOR || $tipoUsuario === TipoUsuario::CONTADOR || $tipoUsuario === TipoUsuario::OBSERVADOR || $tipoUsuario === TipoUsuario::PROCURADOR_MAESTRO) {
            $cantidadAceptadas = $this->contarOrdenesAceptadasActivaGeneral();
        }
        return $cantidadAceptadas;
    }
}
