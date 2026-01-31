<?php

namespace App\Services;

use App\Models\Orden;
use App\Constants\Estado;
use App\Constants\EtapaOrden;
use App\Constants\TipoUsuario;
use Illuminate\Support\Facades\Auth;

class ContadorOrdenGiradasService
{
    //Contador de ordenes giradas
    public function contarOrdenesGiradaActivaUsuario(): int
    {
        $usuarioId = Auth::user()->id;
        return Orden::where('etapa_orden', EtapaOrden::GIRADA)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->whereHas('causa', function ($query) use ($usuarioId) {
                $query->where('usuario_id', $usuarioId);
            })
            ->count();
    }
    public function contarOrdenesGiradaActivaAbogadoDependiente(): int
    {
        $usuarioId = Auth::user()->id;
        return Orden::where('etapa_orden', EtapaOrden::GIRADA)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->whereHas('causa', function ($query) use ($usuarioId) {
                $query->where('abogado_id', $usuarioId);
            })
            ->count();
    }
    public function contarOrdenesGiradaActivaProcurador(): int
    {
        $usuarioId = Auth::user()->id;
        return Orden::where('etapa_orden', EtapaOrden::GIRADA)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('procurador_id', $usuarioId)
            ->count();
    }
    public function contarOrdenesGiradaActivaGeneral(): int
    {
        return Orden::where('etapa_orden', EtapaOrden::GIRADA)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->count();
    }

    public function devuelveCantidadOrdenesGiradas()
    {
        $tipoUsuario = Auth::user()->tipo;
        $cantidadGiradas = 0;
        if ($tipoUsuario === TipoUsuario::ABOGADO_INDEPENDIENTE || $tipoUsuario === TipoUsuario::ABOGADO_LIDER); {
            $cantidadGiradas = $this->contarOrdenesGiradaActivaUsuario();
        }
        if ($tipoUsuario === TipoUsuario::ABOGADO_DEPENDIENTE) {
            $cantidadGiradas = $this->contarOrdenesGiradaActivaAbogadoDependiente();
        }
        if ($tipoUsuario === TipoUsuario::PROCURADOR) {
            $cantidadGiradas = $this->contarOrdenesGiradaActivaProcurador();
        }
        if ($tipoUsuario === TipoUsuario::ADMINISTRADOR || $tipoUsuario === TipoUsuario::CONTADOR || $tipoUsuario === TipoUsuario::OBSERVADOR || $tipoUsuario === TipoUsuario::PROCURADOR_MAESTRO) {
            $cantidadGiradas = $this->contarOrdenesGiradaActivaGeneral();
        }
        return $cantidadGiradas;
    }
}
