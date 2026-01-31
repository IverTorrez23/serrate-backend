<?php

namespace App\Services\SeguimientoLider;

use Carbon\Carbon;
use App\Models\Orden;
use App\Constants\Estado;
use App\Constants\EtapaOrden;
use App\Constants\TipoUsuario;
use Illuminate\Support\Facades\Auth;

class ContadorOrdenesDeLiderService
{
    //Contador de ordenes giradas
    public function contarOrdenesGiradaDeLider(): int
    {
        $usuarioId = Auth::user()->id;
        return Orden::where('etapa_orden', EtapaOrden::GIRADA)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('usuario_id', $usuarioId)
            ->where('girada_por', TipoUsuario::ABOGADO_LIDER)
            ->whereHas('causa', function ($query) use ($usuarioId) {
                $query->where('usuario_id', $usuarioId);
            })
            ->count();
    }
    //Funciones para Contadores de ordenes PRE-PRESUPUESTADAS
    public function contarOrdenesPrePresupuestadasDeLider(): int
    {
        $usuarioId = Auth::user()->id;
        return Orden::where('etapa_orden', EtapaOrden::PREPRESUPUESTADA)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('usuario_id', $usuarioId)
            ->where('girada_por', TipoUsuario::ABOGADO_LIDER)
            ->whereHas('causa', function ($query) use ($usuarioId) {
                $query->where('usuario_id', $usuarioId);
            })
            ->count();
    }
    //Contador de ordenes presupuestadas
    public function contarOrdenesPresupuestadasDeLider(): int
    {
        $usuarioId = Auth::user()->id;
        return Orden::where('etapa_orden', EtapaOrden::PRESUPUESTADA)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('usuario_id', $usuarioId)
            ->where('girada_por', TipoUsuario::ABOGADO_LIDER)
            ->whereHas('causa', function ($query) use ($usuarioId) {
                $query->where('usuario_id', $usuarioId);
            })
            ->count();
    }
    //Contador de ordenes aceptdas (info doc entregados)
    public function contarOrdenesAceptadasDeLider(): int
    {
        $usuarioId = Auth::user()->id;
        return Orden::where('etapa_orden', EtapaOrden::ACEPTADA)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('usuario_id', $usuarioId)
            ->where('girada_por', TipoUsuario::ABOGADO_LIDER)
            ->whereHas('causa', function ($query) use ($usuarioId) {
                $query->where('usuario_id', $usuarioId);
            })
            ->whereHas('presupuesto', function ($query) {
                $query->whereNull('fecha_entrega');
            })
            ->count();
    }
    //Contador de ordenes Dinero entregado
    public function contarOrdenesDineroEntregadoDeLider(): int
    {
        $usuarioId = Auth::user()->id;
        return Orden::where('etapa_orden', EtapaOrden::DINERO_ENTREGADO)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('usuario_id', $usuarioId)
            ->whereNull('fecha_recepcion')
            ->where('girada_por', TipoUsuario::ABOGADO_LIDER)
            ->whereHas('causa', function ($query) use ($usuarioId) {
                $query->where('usuario_id', $usuarioId);
            })
            ->count();
    }
    //Contador de ordenes listas a realizar
    public function contarOrdenesListasRealizarDeLider(): int
    {
        $usuarioId = Auth::user()->id;
        return Orden::whereNotNull('fecha_recepcion')
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('usuario_id', $usuarioId)
            ->where('girada_por', TipoUsuario::ABOGADO_LIDER)
            ->whereDoesntHave('descarga')
            ->whereHas('causa', function ($query) use ($usuarioId) {
                $query->where('usuario_id', $usuarioId);
            })
            ->whereHas('presupuesto', function ($query) {
                $query->whereNotNull('fecha_entrega');
            })
            ->count();
    }
    //Contador de ordenes Descargada
    public function contarOrdenesDescargadaDeLider(): int
    {
        $usuarioId = Auth::user()->id;
        return Orden::where('etapa_orden', EtapaOrden::DESCARGADA)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('usuario_id', $usuarioId)
            ->where('girada_por', TipoUsuario::ABOGADO_LIDER)
            ->whereHas('causa', function ($query) use ($usuarioId) {
                $query->where('usuario_id', $usuarioId);
            })
            ->count();
    }
    //Contador de ordenes pronuncio abogado
    public function contarOrdenesPronuncioAbogadoDeLider(): int
    {
        $usuarioId = Auth::user()->id;
        return Orden::where('etapa_orden', EtapaOrden::PRONUNCIO_ABOGADO)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('usuario_id', $usuarioId)
            ->where('girada_por', TipoUsuario::ABOGADO_LIDER)
            ->whereHas('causa', function ($query) use ($usuarioId) {
                $query->where('usuario_id', $usuarioId);
            })
            ->count();
    }
    //Contador de ordenes cuentas conciliadas
    public function contarOrdenesCuentaConciliadaDeLider(): int
    {
        $usuarioId = Auth::user()->id;
        return Orden::where('etapa_orden', EtapaOrden::PRONUNCIO_CONTADOR)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('usuario_id', $usuarioId)
            ->where('girada_por', TipoUsuario::ABOGADO_LIDER)
            ->whereHas('causa', function ($query) use ($usuarioId) {
                $query->where('usuario_id', $usuarioId);
            })
            ->count();
    }
    //Contador de ordenes vencidas leves (fueron descargadas dentro del plazo, pero aun no estan cerradas y la fecha final ya paso)
    public function contarOrdenesVencidasLevesDeLider(): int
    {
        $now = Carbon::now('America/La_Paz');
        $fechaHora = $now->format('Y-m-d H:i:00');
        $usuarioId = Auth::user()->id;

        return Orden::where('etapa_orden', '!=', EtapaOrden::CERRADA)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('usuario_id', $usuarioId)
            ->where('girada_por', TipoUsuario::ABOGADO_LIDER)
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
    //Contador de ordenes vencidas graves (aun no fueron descargadas y el plazo se vencio)
    public function contarOrdenesVencidasGravesDeLider(): int
    {
        $now = Carbon::now('America/La_Paz');
        $fechaHora = $now->format('Y-m-d H:i:00');
        $usuarioId = Auth::user()->id;

        return Orden::where('etapa_orden', '!=', EtapaOrden::CERRADA)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('usuario_id', $usuarioId)
            ->where('girada_por', TipoUsuario::ABOGADO_LIDER)
            ->where('fecha_fin', '<', $fechaHora)
            ->whereDoesntHave('descarga') //Verifica que no exista registros en descarga
            ->whereHas('causa', function ($query) use ($usuarioId) {
                $query->where('usuario_id', $usuarioId);
            })
            ->count();
    }
}
