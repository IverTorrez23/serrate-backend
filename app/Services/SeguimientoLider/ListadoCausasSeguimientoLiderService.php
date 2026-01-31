<?php

namespace App\Services\SeguimientoLider;

use Carbon\Carbon;
use App\Models\Causa;
use App\Constants\Estado;
use Illuminate\Http\Request;
use App\Constants\EtapaOrden;
use App\Constants\TipoUsuario;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\CausaCollection;

class ListadoCausasSeguimientoLiderService
{
    //Servicio de listado de causas que tienen ordenes giradas
    public function listarCausasConOrdenesGiradasDeLider(Request $request)
    {
        $usuarioId = Auth::user()->id;
        $query = Causa::where('es_eliminado', 0)
            ->where('usuario_id', $usuarioId)
            ->whereHas('ordenes', function ($query) {
                $usuarioId = Auth::user()->id;
                $query->where('etapa_orden', EtapaOrden::GIRADA)
                    ->where('estado', Estado::ACTIVO) // Añade condiciones adicionales si es necesario
                    ->where('usuario_id', $usuarioId)
                    ->where('girada_por', TipoUsuario::ABOGADO_LIDER)
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
    //Servicio de listado de causas que tienen ordenes pre presupuestadas
    public function listarCausasConOrdenesPrePresupuestadasDeLider(Request $request)
    {
        $usuarioId = Auth::user()->id;
        $query = Causa::where('es_eliminado', 0)
            ->where('usuario_id', $usuarioId)
            ->whereHas('ordenes', function ($query) {
                $usuarioId = Auth::user()->id;
                $query->where('etapa_orden', EtapaOrden::PREPRESUPUESTADA)
                    ->where('estado', Estado::ACTIVO) // Añade condiciones adicionales si es necesario
                    ->where('usuario_id', $usuarioId)
                    ->where('girada_por', TipoUsuario::ABOGADO_LIDER)
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
    //Servicio de listado de causas que tienen ordenes presupuestadas
    public function listarCausasConOrdenesPresupuesDeLider(Request $request)
    {
        $usuarioId = Auth::user()->id;
        $query = Causa::where('es_eliminado', 0)
            ->where('usuario_id', $usuarioId)
            ->whereHas('ordenes', function ($query) {
                $usuarioId = Auth::user()->id;
                $query->where('etapa_orden', EtapaOrden::PRESUPUESTADA)
                    ->where('estado', Estado::ACTIVO) // Añade condiciones adicionales si es necesario
                    ->where('usuario_id', $usuarioId)
                    ->where('girada_por', TipoUsuario::ABOGADO_LIDER)
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
    //Servicio de listado de causas que tienen ordenes aceptadas
    public function listarCausasConOrdenesAceptadasDeLider(Request $request)
    {
        $usuarioId = Auth::user()->id;
        $query = Causa::where('es_eliminado', 0)
            ->where('usuario_id', $usuarioId)
            ->whereHas('ordenes', function ($query) {
                $usuarioId = Auth::user()->id;
                $query->where('etapa_orden', EtapaOrden::ACEPTADA)
                    ->where('estado', Estado::ACTIVO) // Añade condiciones adicionales si es necesario
                    ->where('usuario_id', $usuarioId)
                    ->where('girada_por', TipoUsuario::ABOGADO_LIDER)
                    ->where('es_eliminado', 0)
                    ->whereHas('presupuesto', function ($query) {
                        $query->whereNull('fecha_entrega');
                    });
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
    //Servicio de listado de causas que tienen ordenes dinero entregado
    public function listarCausasConOrdenesDineroEntregadoDeLider(Request $request)
    {
        $usuarioId = Auth::user()->id;
        $query = Causa::where('es_eliminado', 0)
            ->where('usuario_id', $usuarioId)
            ->whereHas('ordenes', function ($query) {
                $usuarioId = Auth::user()->id;
                $query->where('etapa_orden', EtapaOrden::DINERO_ENTREGADO)
                    ->whereNull('fecha_recepcion')
                    ->where('estado', Estado::ACTIVO) // Añade condiciones adicionales si es necesario
                    ->where('usuario_id', $usuarioId)
                    ->where('girada_por', TipoUsuario::ABOGADO_LIDER)
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
    //Servicio de listado de causas que tienen ordenes listas realizar
    public function listarCausasConOrdenesListaRealizarDeLider(Request $request)
    {
        $usuarioId = Auth::user()->id;

        $query = Causa::where('es_eliminado', 0)
            ->where('usuario_id', $usuarioId)
            ->whereHas('ordenes', function ($query) {
                $usuarioId = Auth::user()->id;
                $query->whereNotNull('fecha_recepcion')
                    ->where('estado', Estado::ACTIVO) // Añade condiciones adicionales si es necesario
                    ->where('usuario_id', $usuarioId)
                    ->where('girada_por', TipoUsuario::ABOGADO_LIDER)
                    ->where('es_eliminado', 0)
                    ->whereDoesntHave('descarga')
                    ->whereHas('presupuesto', function ($query) {
                        $query->whereNotNull('fecha_entrega');
                    });
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
    //Servicio de listado de causas que tienen ordenes descargada
    public function listarCausasConOrdenesDescargadasDeLider(Request $request)
    {
        $usuarioId = Auth::user()->id;
        $query = Causa::where('es_eliminado', 0)
            ->where('usuario_id', $usuarioId)
            ->whereHas('ordenes', function ($query) {
                $usuarioId = Auth::user()->id;
                $query->where('etapa_orden', EtapaOrden::DESCARGADA)
                    ->where('estado', Estado::ACTIVO) // Añade condiciones adicionales si es necesario
                    ->where('usuario_id', $usuarioId)
                    ->where('girada_por', TipoUsuario::ABOGADO_LIDER)
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
    //Servicio de listado de causas que tienen ordenes pronuncio abogado
    public function listarCausasConOrdenesPronuncioAbogadoDeLider(Request $request)
    {
        $usuarioId = Auth::user()->id;
        $query = Causa::where('es_eliminado', 0)
            ->where('usuario_id', $usuarioId)
            ->whereHas('ordenes', function ($query) {
                $usuarioId = Auth::user()->id;
                $query->where('etapa_orden', EtapaOrden::PRONUNCIO_ABOGADO)
                    ->where('estado', Estado::ACTIVO) // Añade condiciones adicionales si es necesario
                    ->where('usuario_id', $usuarioId)
                    ->where('girada_por', TipoUsuario::ABOGADO_LIDER)
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
    //Servicio de listado de causas que tienen ordenes cuentas conciliadas
    public function listarCausasConOrdenesCuentasConciliadasDeLider(Request $request)
    {
        $usuarioId = Auth::user()->id;
        $query = Causa::where('es_eliminado', 0)
            ->where('usuario_id', $usuarioId)
            ->whereHas('ordenes', function ($query) {
                $usuarioId = Auth::user()->id;
                $query->where('etapa_orden', EtapaOrden::PRONUNCIO_CONTADOR)
                    ->where('estado', Estado::ACTIVO) // Añade condiciones adicionales si es necesario
                    ->where('usuario_id', $usuarioId)
                    ->where('girada_por', TipoUsuario::ABOGADO_LIDER)
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
    //Servicio de listado de causas que tienen ordenes vencidas leves
    public function listarCausasConOrdenesVencidasLevesDeLider(Request $request)
    {
        $usuarioId = Auth::user()->id;
        $query = Causa::where('es_eliminado', 0)
            ->where('usuario_id', $usuarioId)
            ->whereHas('ordenes', function ($query) {
                $usuarioId = Auth::user()->id;
                $now = Carbon::now('America/La_Paz');
                $fechaHora = $now->format('Y-m-d H:i:00');

                $query->where('etapa_orden', '!=', EtapaOrden::CERRADA)
                    ->where('estado', Estado::ACTIVO) // Añade condiciones adicionales si es necesario
                    ->where('usuario_id', $usuarioId)
                    ->where('girada_por', TipoUsuario::ABOGADO_LIDER)
                    ->where('es_eliminado', 0)
                    ->where('fecha_fin', '<=', $fechaHora)
                    ->whereHas('descarga', function ($query) {
                        $query->whereHas('confirmacion', function ($query) {
                            $query->where('confir_sistema', 1);
                        });
                    });
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
    //Servicio de listado de causas que tienen ordenes vencidas  graves
    public function listarCausasConOrdenesVencidasGravesDeLider(Request $request)
    {
        $usuarioId = Auth::user()->id;
        $query = Causa::where('es_eliminado', 0)
            ->where('usuario_id', $usuarioId)
            ->whereHas('ordenes', function ($query) {
                $usuarioId = Auth::user()->id;
                $now = Carbon::now('America/La_Paz');
                $fechaHora = $now->format('Y-m-d H:i:00');
                $query->where('etapa_orden', '!=', EtapaOrden::CERRADA)
                    ->where('estado', Estado::ACTIVO) // Añade condiciones adicionales si es necesario
                    ->where('usuario_id', $usuarioId)
                    ->where('girada_por', TipoUsuario::ABOGADO_LIDER)
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
}
