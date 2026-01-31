<?php

namespace App\Services;

use App\Models\Causa;
use App\Constants\Estado;
use Illuminate\Http\Request;
use App\Constants\EtapaOrden;
use App\Constants\TipoUsuario;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\CausaCollection;

class ListadoCausasOrdenDineroEntregadoService
{
    //Servicio de listado de causas que tienen ordenes dinero entregado
    public function listarCausasConOrdenesDineroEntregadoUsuario(Request $request)
    {
        $usuarioId = Auth::user()->id;
        $query = Causa::where('es_eliminado', 0)
            ->where('usuario_id', $usuarioId)
            ->whereHas('ordenes', function ($query) {
                $query->where('etapa_orden', EtapaOrden::DINERO_ENTREGADO)
                    ->whereNull('fecha_recepcion')
                    ->where('estado', Estado::ACTIVO) // Añade condiciones adicionales si es necesario
                    ->where('es_eliminado', 0);
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

    public function listarCausasConOrdenesDineroEntregadoAbogado(Request $request)
    {
        $usuarioId = Auth::user()->id;
        $query = Causa::where('es_eliminado', 0)
            ->where('abogado_id', $usuarioId)
            ->whereHas('ordenes', function ($query) {
                $query->where('etapa_orden', EtapaOrden::DINERO_ENTREGADO)
                    ->whereNull('fecha_recepcion')
                    ->where('estado', Estado::ACTIVO) // Añade condiciones adicionales si es necesario
                    ->where('es_eliminado', 0);
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

    public function listarCausasConOrdenesDineroEntregadoProcurador(Request $request)
    {
        $query = Causa::where('es_eliminado', 0)

            ->whereHas('ordenes', function ($query) {
                $usuarioId = Auth::user()->id;

                $query->where('etapa_orden', EtapaOrden::DINERO_ENTREGADO)
                    ->whereNull('fecha_recepcion')
                    ->where('estado', Estado::ACTIVO) // Añade condiciones adicionales si es necesario
                    ->where('es_eliminado', 0)
                    ->where('procurador_id', $usuarioId);
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
    public function listarCausasConOrdenesDineroEntregadoGeneral(Request $request)
    {
        $query = Causa::where('es_eliminado', 0)
            ->whereHas('ordenes', function ($query) {
                $query->where('etapa_orden', EtapaOrden::DINERO_ENTREGADO)
                    ->whereNull('fecha_recepcion')
                    ->where('estado', Estado::ACTIVO) // Añade condiciones adicionales si es necesario
                    ->where('es_eliminado', 0);
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



    public function devuelveListadoCausasOrdenDineroEntregado(Request $request)
    {
        $tipoUsuario = Auth::user()->tipo;
        if ($tipoUsuario === TipoUsuario::ABOGADO_INDEPENDIENTE || $tipoUsuario === TipoUsuario::ABOGADO_LIDER) {
            $causas = $this->listarCausasConOrdenesDineroEntregadoUsuario($request);
        }
        if ($tipoUsuario === TipoUsuario::ABOGADO_DEPENDIENTE) {
            $causas = $this->listarCausasConOrdenesDineroEntregadoAbogado($request);
        }
        if ($tipoUsuario === TipoUsuario::PROCURADOR) {
            $causas = $this->listarCausasConOrdenesDineroEntregadoProcurador($request);
        }
        if ($tipoUsuario === TipoUsuario::ADMINISTRADOR || $tipoUsuario === TipoUsuario::CONTADOR || $tipoUsuario === TipoUsuario::OBSERVADOR || $tipoUsuario === TipoUsuario::PROCURADOR_MAESTRO) {
            $causas = $this->listarCausasConOrdenesDineroEntregadoGeneral($request);
        }
        return $causas;
    }
}
