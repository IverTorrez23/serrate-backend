<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Causa;
use App\Constants\Estado;
use Illuminate\Http\Request;
use App\Constants\EtapaOrden;
use App\Constants\TipoUsuario;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\CausaCollection;

class ListadoCausasOrdenVencidasGravesService
{
    //Servicio de listado de causas que tienen ordenes vencidas  graves
    public function listarCausasConOrdenesVencidasGravesUsuario(Request $request)
    {
        $usuarioId = Auth::user()->id;
        $query = Causa::where('es_eliminado', 0)
            ->where('usuario_id', $usuarioId)
            ->whereHas('ordenes', function ($query) {
                $now = Carbon::now('America/La_Paz');
                $fechaHora = $now->format('Y-m-d H:i:00');
                $query->where('etapa_orden', '!=', EtapaOrden::CERRADA)
                    ->where('estado', Estado::ACTIVO) // Añade condiciones adicionales si es necesario
                    ->where('es_eliminado', 0)
                    ->where('fecha_fin', '<', $fechaHora)
                    ->whereDoesntHave('descarga'); //Verifica que no exista registros en descarga
            });

        // Manejo de búsqueda
        if ($request->has('search')) {
            $search = json_decode($request->input('search'), true);
            $query->search($search);
        }

        // Manejo de ordenamiento
        if ($request->has('sort')) {
            $sort = json_decode($request->input('sort'), true);
            $query->sort($sort);
        }

        $perPage = $request->input('perPage', 10);
        $causas = $query->paginate($perPage);

        $causas->load('materia');
        $causas->load('tipoLegal');
        $causas->load('categoria');
        $causas->load('abogado.persona');
        $causas->load('procurador.persona');
        return new CausaCollection($causas);
    }

    public function listarCausasConOrdenesVencidasGravesAbogado(Request $request)
    {
        $usuarioId = Auth::user()->id;
        $query = Causa::where('es_eliminado', 0)
            ->where('abogado_id', $usuarioId)
            ->whereHas('ordenes', function ($query) {
                $now = Carbon::now('America/La_Paz');
                $fechaHora = $now->format('Y-m-d H:i:00');

                $query->where('etapa_orden', '!=', EtapaOrden::CERRADA)
                    ->where('estado', Estado::ACTIVO) // Añade condiciones adicionales si es necesario
                    ->where('es_eliminado', 0)
                    ->where('fecha_fin', '<', $fechaHora)
                    ->whereDoesntHave('descarga');
            });

        // Manejo de búsqueda
        if ($request->has('search')) {
            $search = json_decode($request->input('search'), true);
            $query->search($search);
        }

        // Manejo de ordenamiento
        if ($request->has('sort')) {
            $sort = json_decode($request->input('sort'), true);
            $query->sort($sort);
        }

        $perPage = $request->input('perPage', 10);
        $causas = $query->paginate($perPage);

        $causas->load('materia');
        $causas->load('tipoLegal');
        $causas->load('categoria');
        $causas->load('abogado.persona');
        $causas->load('procurador.persona');
        return new CausaCollection($causas);
    }

    public function listarCausasConOrdenesVencidasGravesProcurador(Request $request)
    {
        $query = Causa::where('es_eliminado', 0)

            ->whereHas('ordenes', function ($query) {
                $usuarioId = Auth::user()->id;
                $now = Carbon::now('America/La_Paz');
                $fechaHora = $now->format('Y-m-d H:i:00');

                $query->where('etapa_orden', '!=', EtapaOrden::CERRADA)
                    ->where('estado', Estado::ACTIVO) // Añade condiciones adicionales si es necesario
                    ->where('es_eliminado', 0)
                    ->where('procurador_id', $usuarioId)
                    ->where('fecha_fin', '<', $fechaHora)
                    ->whereDoesntHave('descarga');
            });

        // Manejo de búsqueda
        if ($request->has('search')) {
            $search = json_decode($request->input('search'), true);
            $query->search($search);
        }

        // Manejo de ordenamiento
        if ($request->has('sort')) {
            $sort = json_decode($request->input('sort'), true);
            $query->sort($sort);
        }

        $perPage = $request->input('perPage', 10);
        $causas = $query->paginate($perPage);

        $causas->load('materia');
        $causas->load('tipoLegal');
        $causas->load('categoria');
        $causas->load('abogado.persona');
        $causas->load('procurador.persona');
        return new CausaCollection($causas);
    }
    public function listarCausasConOrdenesVencidasGravesGeneral(Request $request)
    {
        $query = Causa::where('es_eliminado', 0)
            ->whereHas('ordenes', function ($query) {
                $now = Carbon::now('America/La_Paz');
                $fechaHora = $now->format('Y-m-d H:i:00');
                $query->where('etapa_orden', '!=', EtapaOrden::CERRADA)
                    ->where('estado', Estado::ACTIVO) // Añade condiciones adicionales si es necesario
                    ->where('es_eliminado', 0)
                    ->where('fecha_fin', '<', $fechaHora)
                    ->whereDoesntHave('descarga');
            });

        // Manejo de búsqueda
        if ($request->has('search')) {
            $search = json_decode($request->input('search'), true);
            $query->search($search);
        }

        // Manejo de ordenamiento
        if ($request->has('sort')) {
            $sort = json_decode($request->input('sort'), true);
            $query->sort($sort);
        }

        $perPage = $request->input('perPage', 10);
        $causas = $query->paginate($perPage);

        $causas->load('materia');
        $causas->load('tipoLegal');
        $causas->load('categoria');
        $causas->load('abogado.persona');
        $causas->load('procurador.persona');
        return new CausaCollection($causas);
    }



    public function devuelveListadoCausasOrdenVencidasGraves(Request $request)
    {
        $tipoUsuario = Auth::user()->tipo;
        if ($tipoUsuario === TipoUsuario::ABOGADO_INDEPENDIENTE || $tipoUsuario === TipoUsuario::ABOGADO_LIDER) {
            $causas = $this->listarCausasConOrdenesVencidasGravesUsuario($request);
        }
        if ($tipoUsuario === TipoUsuario::ABOGADO_DEPENDIENTE) {
            $causas = $this->listarCausasConOrdenesVencidasGravesAbogado($request);
        }
        if ($tipoUsuario === TipoUsuario::PROCURADOR) {
            $causas = $this->listarCausasConOrdenesVencidasGravesProcurador($request);
        }
        if ($tipoUsuario === TipoUsuario::ADMINISTRADOR || $tipoUsuario === TipoUsuario::CONTADOR || $tipoUsuario === TipoUsuario::OBSERVADOR || $tipoUsuario === TipoUsuario::PROCURADOR_MAESTRO) {
            $causas = $this->listarCausasConOrdenesVencidasGravesGeneral($request);
        }
        return $causas;
    }
}
