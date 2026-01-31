<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Causa;
use App\Constants\Estado;
use App\Models\TipoLegal;
use App\Enums\MessageHttp;
use Illuminate\Http\Request;
use App\Constants\EtapaOrden;
use App\Services\UserService;
use App\Constants\EstadoCausa;
use App\Constants\FechaHelper;
use App\Constants\TipoUsuario;
use App\Services\CausaService;
use App\Services\PostaService;
use Illuminate\Support\Facades\DB;

use App\Services\CausaPostaService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\PaqueteCausaService;
use App\Http\Resources\CausaCollection;
use App\Http\Requests\StoreCausaRequest;
use App\Services\AvancePlantillaService;
use App\Http\Requests\UpdateCausaRequest;
use App\Services\ListadoCausasOrdenGiradaService;
use App\Services\ListadoCausasOrdenAceptadasService;
use App\Services\ListadoCausasOrdenDescargadaService;
use App\Services\ListadoCausasOrdenListaRealizarService;
use App\Services\ListadoCausasOrdenVencidasLevesService;
use App\Services\ListadoCausasOrdenPresupuestadasService;
use App\Services\ListadoCausasOrdenVencidasGravesService;
use App\Services\ListadoCausasOrdenDineroEntregadoService;
use App\Services\ListadoCausasOrdenPronuncioAbogadoService;
use App\Services\ListadoCausasOrdenPrePresupuestadasService;
use App\Services\ListadoCausasOrdenCuentasConciliadasService;
use App\Services\SeguimientoLider\ListadoCausasSeguimientoLiderService;
use App\Services\ParametroVigenciaService;

class CausaController extends Controller
{
    protected $causaService;
    protected $avancePlantillaService;
    protected $postaService;
    protected $causaPostaService;
    protected $userService;
    protected $paqueteCausaService;
    protected $listadoCausasOrdenGiradaService;
    protected $listadoCausasOrdenPrePresupuestadasService;
    protected $listadoCausasOrdenPresupuestadasService;
    protected $listadoCausasOrdenAceptadasService;
    protected $listadoCausasOrdenDineroEntregadoService;
    protected $listadoCausasOrdenListaRealizarService;
    protected $listadoCausasOrdenDescargadaService;
    protected $listadoCausasOrdenPronuncioAbogadoService;
    protected $listadoCausasOrdenCuentasConciliadaService;
    protected $listadoCausasOrdenVencidasLevesService;
    protected $listadoCausasOrdenVencidasGravesService;
    protected $parametroVigenciaService;
    //Servicios para seguimiento para Lider
    protected $listadoCausasSeguimientoLiderService;

    public function __construct(
        CausaService $causaService,
        AvancePlantillaService $avancePlantillaService,
        PostaService $postaService,
        CausaPostaService $causaPostaService,
        UserService $userService,
        PaqueteCausaService $paqueteCausaService,
        ListadoCausasOrdenGiradaService $listadoCausasOrdenGiradaService,
        ListadoCausasOrdenPrePresupuestadasService $listadoCausasOrdenPrePresupuestadasService,
        ListadoCausasOrdenPresupuestadasService $listadoCausasOrdenPresupuestadasService,
        ListadoCausasOrdenAceptadasService $listadoCausasOrdenAceptadasService,
        ListadoCausasOrdenDineroEntregadoService $listadoCausasOrdenDineroEntregadoService,
        ListadoCausasOrdenListaRealizarService $listadoCausasOrdenListaRealizarService,
        ListadoCausasOrdenDescargadaService $listadoCausasOrdenDescargadaService,
        ListadoCausasOrdenPronuncioAbogadoService $listadoCausasOrdenPronuncioAbogadoService,
        ListadoCausasOrdenCuentasConciliadasService $listadoCausasOrdenCuentasConciliadaService,
        ListadoCausasOrdenVencidasLevesService $listadoCausasOrdenVencidasLevesService,
        ListadoCausasOrdenVencidasGravesService $listadoCausasOrdenVencidasGravesService,
        //Listado para user lider
        ListadoCausasSeguimientoLiderService $listadoCausasSeguimientoLiderService,
        ParametroVigenciaService $parametroVigenciaService
    ) {
        $this->causaService = $causaService;
        $this->avancePlantillaService = $avancePlantillaService;
        $this->postaService = $postaService;
        $this->causaPostaService = $causaPostaService;
        $this->userService = $userService;
        $this->paqueteCausaService = $paqueteCausaService;
        $this->listadoCausasOrdenGiradaService = $listadoCausasOrdenGiradaService;
        $this->listadoCausasOrdenPrePresupuestadasService = $listadoCausasOrdenPrePresupuestadasService;
        $this->listadoCausasOrdenPresupuestadasService = $listadoCausasOrdenPresupuestadasService;
        $this->listadoCausasOrdenAceptadasService = $listadoCausasOrdenAceptadasService;
        $this->listadoCausasOrdenDineroEntregadoService = $listadoCausasOrdenDineroEntregadoService;
        $this->listadoCausasOrdenListaRealizarService = $listadoCausasOrdenListaRealizarService;
        $this->listadoCausasOrdenDescargadaService = $listadoCausasOrdenDescargadaService;
        $this->listadoCausasOrdenPronuncioAbogadoService = $listadoCausasOrdenPronuncioAbogadoService;
        $this->listadoCausasOrdenCuentasConciliadaService = $listadoCausasOrdenCuentasConciliadaService;
        $this->listadoCausasOrdenVencidasLevesService = $listadoCausasOrdenVencidasLevesService;
        $this->listadoCausasOrdenVencidasGravesService = $listadoCausasOrdenVencidasGravesService;
        //Lista para user lider
        $this->listadoCausasSeguimientoLiderService = $listadoCausasSeguimientoLiderService;
        $this->parametroVigenciaService = $parametroVigenciaService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $fechaHoraSistema = FechaHelper::fechaHoraBolivia();
        //$query = Causa::active();
        $query = Causa::where('estado', '!=', EstadoCausa::TERMINADA)
            ->where('es_eliminado', 0);

        $usuario = Auth::user();
        //Filtrado por usuario
        if ($usuario->tipo === TipoUsuario::ABOGADO_LIDER || $usuario->tipo === TipoUsuario::ABOGADO_INDEPENDIENTE) {
            $query->where('usuario_id', $usuario->id);
        } else {
            if ($usuario->tipo === TipoUsuario::ABOGADO_DEPENDIENTE) {
                $query->where('abogado_id', $usuario->id);
            } else {
                if ($usuario->tipo === TipoUsuario::PROCURADOR) {
                    $query->where('procurador_id', $usuario->id);
                }
            }
        }


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
        //Listado para procuradores
        if ($usuario->tipo === TipoUsuario::PROCURADOR) {
            $causas->load([
                'ordenes' => function ($query) use ($usuario, $fechaHoraSistema) {
                    $query->select('id', 'fecha_inicio', 'fecha_fin', 'prioridad', 'causa_id') // Incluye causa_id para la relación
                        ->where('estado', Estado::ACTIVO)
                        ->where('es_eliminado', 0)
                        ->where('procurador_id', $usuario->id)
                        ->where('fecha_inicio', '<=', $fechaHoraSistema)
                        ->whereDoesntHave('descarga');
                }
            ]);
        } else { //Listado para el resto de usuarios
            $causas->load([
                'ordenes' => function ($query) use ($fechaHoraSistema) {
                    $query->select('id', 'fecha_inicio', 'fecha_fin', 'prioridad', 'causa_id') // Incluye causa_id para la relación
                        ->where('estado', Estado::ACTIVO)
                        ->where('es_eliminado', 0)
                        ->where('fecha_inicio', '<=', $fechaHoraSistema)
                        ->whereDoesntHave('descarga');
                }
            ]);
        }


        return new CausaCollection($causas);
    }
    public function listadoPorCodigoLegal(Request $request, $codigoLegal)
    {
        $fechaHoraSistema = FechaHelper::fechaHoraBolivia();
        //$query = Causa::active();
        $query = Causa::where('estado', '!=', EstadoCausa::TERMINADA)
            ->where('es_eliminado', 0)
            ->where('tipolegal_id', $codigoLegal);

        $usuario = Auth::user();
        //Filtrado por usuario
        if ($usuario->tipo === TipoUsuario::ABOGADO_LIDER || $usuario->tipo === TipoUsuario::ABOGADO_INDEPENDIENTE) {
            $query->where('usuario_id', $usuario->id);
        } else {
            if ($usuario->tipo === TipoUsuario::ABOGADO_DEPENDIENTE) {
                $query->where('abogado_id', $usuario->id);
            } else {
                if ($usuario->tipo === TipoUsuario::PROCURADOR) {
                    $query->where('procurador_id', $usuario->id);
                }
            }
        }


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
        //Listado para procuradores
        if ($usuario->tipo === TipoUsuario::PROCURADOR) {
            $causas->load([
                'ordenes' => function ($query) use ($usuario, $fechaHoraSistema) {
                    $query->select('id', 'fecha_inicio', 'fecha_fin', 'prioridad', 'causa_id') // Incluye causa_id para la relación
                        ->where('estado', Estado::ACTIVO)
                        ->where('es_eliminado', 0)
                        ->where('procurador_id', $usuario->id)
                        ->where('fecha_inicio', '<=', $fechaHoraSistema)
                        ->whereDoesntHave('descarga');
                }
            ]);
        } else { //Listado para el resto de usuarios
            $causas->load([
                'ordenes' => function ($query) use ($fechaHoraSistema) {
                    $query->select('id', 'fecha_inicio', 'fecha_fin', 'prioridad', 'causa_id') // Incluye causa_id para la relación
                        ->where('estado', Estado::ACTIVO)
                        ->where('es_eliminado', 0)
                        ->where('fecha_inicio', '<=', $fechaHoraSistema)
                        ->whereDoesntHave('descarga');
                }
            ]);
        }


        return new CausaCollection($causas);
    }
    public function listadoPorAbogado(Request $request, $abogadoId)
    {
        $fechaHoraSistema = FechaHelper::fechaHoraBolivia();
        //$query = Causa::active();
        $query = Causa::where('estado', '!=', EstadoCausa::TERMINADA)
            ->where('es_eliminado', 0)
            ->where('abogado_id', $abogadoId);

        $usuario = Auth::user();
        //Filtrado por usuario
        if ($usuario->tipo === TipoUsuario::ABOGADO_LIDER || $usuario->tipo === TipoUsuario::ABOGADO_INDEPENDIENTE) {
            $query->where('usuario_id', $usuario->id);
        } else {
            if ($usuario->tipo === TipoUsuario::ABOGADO_DEPENDIENTE) {
                $query->where('abogado_id', $usuario->id);
            } else {
                if ($usuario->tipo === TipoUsuario::PROCURADOR) {
                    $query->where('procurador_id', $usuario->id);
                }
            }
        }


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
        //Listado para procuradores
        if ($usuario->tipo === TipoUsuario::PROCURADOR) {
            $causas->load([
                'ordenes' => function ($query) use ($usuario, $fechaHoraSistema) {
                    $query->select('id', 'fecha_inicio', 'fecha_fin', 'prioridad', 'causa_id') // Incluye causa_id para la relación
                        ->where('estado', Estado::ACTIVO)
                        ->where('es_eliminado', 0)
                        ->where('procurador_id', $usuario->id)
                        ->where('fecha_inicio', '<=', $fechaHoraSistema)
                        ->whereDoesntHave('descarga');
                }
            ]);
        } else { //Listado para el resto de usuarios
            $causas->load([
                'ordenes' => function ($query) use ($fechaHoraSistema) {
                    $query->select('id', 'fecha_inicio', 'fecha_fin', 'prioridad', 'causa_id') // Incluye causa_id para la relación
                        ->where('estado', Estado::ACTIVO)
                        ->where('es_eliminado', 0)
                        ->where('fecha_inicio', '<=', $fechaHoraSistema)
                        ->whereDoesntHave('descarga');
                }
            ]);
        }


        return new CausaCollection($causas);
    }
    public function listadoPorCategoria(Request $request, $categoriaId)
    {
        $fechaHoraSistema = FechaHelper::fechaHoraBolivia();
        //$query = Causa::active();
        $query = Causa::where('estado', '!=', EstadoCausa::TERMINADA)
            ->where('es_eliminado', 0)
            ->where('categoria_id', $categoriaId);

        $usuario = Auth::user();
        //Filtrado por usuario
        if ($usuario->tipo === TipoUsuario::ABOGADO_LIDER || $usuario->tipo === TipoUsuario::ABOGADO_INDEPENDIENTE) {
            $query->where('usuario_id', $usuario->id);
        } else {
            if ($usuario->tipo === TipoUsuario::ABOGADO_DEPENDIENTE) {
                $query->where('abogado_id', $usuario->id);
            } else {
                if ($usuario->tipo === TipoUsuario::PROCURADOR) {
                    $query->where('procurador_id', $usuario->id);
                }
            }
        }


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
        //Listado para procuradores
        if ($usuario->tipo === TipoUsuario::PROCURADOR) {
            $causas->load([
                'ordenes' => function ($query) use ($usuario, $fechaHoraSistema) {
                    $query->select('id', 'fecha_inicio', 'fecha_fin', 'prioridad', 'causa_id') // Incluye causa_id para la relación
                        ->where('estado', Estado::ACTIVO)
                        ->where('es_eliminado', 0)
                        ->where('procurador_id', $usuario->id)
                        ->where('fecha_inicio', '<=', $fechaHoraSistema)
                        ->whereDoesntHave('descarga');
                }
            ]);
        } else { //Listado para el resto de usuarios
            $causas->load([
                'ordenes' => function ($query) use ($fechaHoraSistema) {
                    $query->select('id', 'fecha_inicio', 'fecha_fin', 'prioridad', 'causa_id') // Incluye causa_id para la relación
                        ->where('estado', Estado::ACTIVO)
                        ->where('es_eliminado', 0)
                        ->where('fecha_inicio', '<=', $fechaHoraSistema)
                        ->whereDoesntHave('descarga');
                }
            ]);
        }


        return new CausaCollection($causas);
    }

    public function listadoPorProcurador(Request $request, $procuradorId)
    {
        $fechaHoraSistema = FechaHelper::fechaHoraBolivia();
        //$query = Causa::active();
        $query = Causa::where('estado', '!=', EstadoCausa::TERMINADA)
            ->where('es_eliminado', 0)
            ->where('procurador_id', $procuradorId);

        $usuario = Auth::user();
        //Filtrado por usuario
        if ($usuario->tipo === TipoUsuario::ABOGADO_LIDER || $usuario->tipo === TipoUsuario::ABOGADO_INDEPENDIENTE) {
            $query->where('usuario_id', $usuario->id);
        } else {
            if ($usuario->tipo === TipoUsuario::ABOGADO_DEPENDIENTE) {
                $query->where('abogado_id', $usuario->id);
            } else {
                if ($usuario->tipo === TipoUsuario::PROCURADOR) {
                    $query->where('procurador_id', $usuario->id);
                }
            }
        }


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
        //Listado para procuradores
        if ($usuario->tipo === TipoUsuario::PROCURADOR) {
            $causas->load([
                'ordenes' => function ($query) use ($usuario, $fechaHoraSistema) {
                    $query->select('id', 'fecha_inicio', 'fecha_fin', 'prioridad', 'causa_id') // Incluye causa_id para la relación
                        ->where('estado', Estado::ACTIVO)
                        ->where('es_eliminado', 0)
                        ->where('procurador_id', $usuario->id)
                        ->where('fecha_inicio', '<=', $fechaHoraSistema)
                        ->whereDoesntHave('descarga');
                }
            ]);
        } else { //Listado para el resto de usuarios
            $causas->load([
                'ordenes' => function ($query) use ($fechaHoraSistema) {
                    $query->select('id', 'fecha_inicio', 'fecha_fin', 'prioridad', 'causa_id') // Incluye causa_id para la relación
                        ->where('estado', Estado::ACTIVO)
                        ->where('es_eliminado', 0)
                        ->where('fecha_inicio', '<=', $fechaHoraSistema)
                        ->whereDoesntHave('descarga');
                }
            ]);
        }


        return new CausaCollection($causas);
    }
    public function indexTerminadas(Request $request)
    {
        $query = Causa::where('estado', EstadoCausa::TERMINADA)
            ->where('es_eliminado', 0);

        $usuario = Auth::user();
        //Filtrado por usuario
        if ($usuario->tipo === TipoUsuario::ABOGADO_LIDER || $usuario->tipo === TipoUsuario::ABOGADO_INDEPENDIENTE) {
            $query->where('usuario_id', $usuario->id);
        } else {
            if ($usuario->tipo === TipoUsuario::ABOGADO_DEPENDIENTE) {
                $query->where('abogado_id', $usuario->id);
            } else {
                if ($usuario->tipo === TipoUsuario::PROCURADOR) {
                    $query->where('procurador_id', $usuario->id);
                }
            }
        }


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
    public function indexCausasAdmin(Request $request)
    {
        $query = Causa::whereIn('estado', [EstadoCausa::ACTIVA, EstadoCausa::CONGELADA, EstadoCausa::TERMINADA, EstadoCausa::BLOQUEADA])
            ->where('es_eliminado', 0);
        $usuario = Auth::user();
        //Filtrado por usuario
        if ($usuario->tipo === TipoUsuario::ABOGADO_LIDER || $usuario->tipo === TipoUsuario::ABOGADO_INDEPENDIENTE) {
            $query->where('usuario_id', $usuario->id);
        } else {
            if ($usuario->tipo === TipoUsuario::ABOGADO_DEPENDIENTE) {
                $query->where('abogado_id', $usuario->id);
            } else {
                if ($usuario->tipo === TipoUsuario::PROCURADOR) {
                    $query->where('procurador_id', $usuario->id);
                }
            }
        }


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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCausaRequest $request)
    {
        DB::beginTransaction();
        try {
            $fechaHoraSistema = FechaHelper::fechaHoraBolivia();
            $usuarioPmaestro = $this->userService->obtenerUnPMaestro();
            $idUser = Auth::user()->id;
            $tipoUsuario = Auth::user()->tipo;
            $idUserParametroVigencia = $idUser;
            /*if (Auth::user()->tipo === TipoUsuario::ABOGADO_LIDER) {
                $procuradorId = $request->procurador_id;
                $abogadoId = $request->abogado_id;
            } else {
                $procuradorId = $usuarioPmaestro->id;
                $abogadoId = $idUser;
            }*/
            if ($tipoUsuario === TipoUsuario::ABOGADO_DEPENDIENTE) {
                $idUser = Auth::user()->abogado_id; //Id de su abogado lider
                $idUserParametroVigencia = $idUser;
            }
            $parametroVigencia = $this->parametroVigenciaService->obtenerUnoPorUsuario($idUserParametroVigencia);
            if ($fechaHoraSistema > $parametroVigencia->fecha_ultima_vigencia) {
                return response()->json([
                    'message' => 'No existe paquete vigente para hacer operaciones',
                    'data' => null
                ], 409);
            }
            //Asignacion de color
            if (empty($request->color)) {
                $color = '#ffffff';
            } else {
                $color = $request->color;
            }

            $data = [
                'nombre' => $request->nombre,
                'observacion' => $request->observacion,
                'objetivos' => '',
                'estrategia' => '',
                'informacion' => '',
                'apuntes_juridicos' => '',
                'apuntes_honorarios' => '',
                'tiene_billetera' => $request->tiene_billetera,
                'billetera' => 0,
                'saldo_devuelto' => 0,
                'color' => $color,
                'materia_id' => $request->materia_id,
                'tipolegal_id' => $request->tipolegal_id,
                'categoria_id' => $request->categoria_id,
                'abogado_id' => $request->abogado_id,
                'procurador_id' => $request->procurador_id,
                'usuario_id' => $idUser,
                'motivo_congelada' => '',
            ];
            $data['plantilla_id'] = $request->has('plantilla_id') ? $request->plantilla_id : 0;
            $causa = $this->causaService->store($data);

            //SI ELIGIO UNA PLANTILLA DE AVANCE SE REGISTRA EN LA TABLA causa_postas
            if ($request->has('plantilla_id') && $request->plantilla_id > 0) {
                $plantilla_id = $request->plantilla_id;
                $causaPosta = $this->guardarCausaPosta($causa->id, $plantilla_id);
            }

            DB::commit();
            return response()->json([
                'message' => MessageHttp::CREADO_CORRECTAMENTE,
                'data' => $causa
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error registrar causa: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error registrar causa',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Causa $causa)
    {
        $tipoUsuario = Auth::user()->tipo;
        if ($tipoUsuario === TipoUsuario::ABOGADO_INDEPENDIENTE || $tipoUsuario === TipoUsuario::ABOGADO_LIDER || $tipoUsuario === TipoUsuario::ABOGADO_DEPENDIENTE) {
            if (!$this->causaService->abogadoTienePermisoCausa($causa->id)) {
                return response()->json(['message' => 'No esta autorizado para ver estos datos'], 403);
            }
        }
        $causa = $this->causaService->obtenerUno($causa->id);
        $data = [
            'message' => MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data' => $causa
        ];
        return response()->json($data);
    }
    public function listarActivos()
    {
        $causas = $this->causaService->listarActivos();
        $data = [
            'message' => MessageHttp::OBTENIDOS_CORRECTAMENTE,
            'data' => $causas
        ];
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Causa $causa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCausaRequest $request, Causa $causa)
    {
        //Verificacion si se esta terminando la causa
        if ($request->estado === EstadoCausa::TERMINADA && $this->causaService->tieneOrdenesNoCerradas($causa->id)) {
            return response()->json([
                'message' => 'No se puede Terminar la causa porque tiene órdenes no cerradas.',
                'data' => null
            ], 409);
        }
        DB::beginTransaction();
        try {
            $data = $request->only([
                'nombre',
                'observacion',
                'objetivos',
                'estrategia',
                'informacion',
                'apuntes_juridicos',
                'apuntes_honorarios',

                'color',
                'materia_id',
                'tipolegal_id',
                'categoria_id',
                'abogado_id',
                'procurador_id',
                'estado',
                'motivo_congelada',
                'es_eliminado'
            ]);
            $plantillaId = $request->plantilla_id;
            //Pregunta si plantilla_id que esta en el request es diferente al valor de plantilla_id de la causa (si eligio otra plantilla)
            if ($request->has('plantilla_id') && $plantillaId > 0 && $plantillaId != $causa->plantilla_id) {
                $data['plantilla_id'] = $plantillaId;
                if ($causa->plantilla_id > 0) {
                    $this->causaPostaService->eliminarPorCausaId($causa->id);
                }
                $causaPosta = $this->guardarCausaPosta($causa->id, $plantillaId);
            }
            $causa = $this->causaService->update($data, $causa->id);

            //Si se esta terminando la causa se elimina del paquete
            if ($request->estado === EstadoCausa::TERMINADA) {
                //Elimina la causa de PaqueteCausa (si esxistiera)
                $this->paqueteCausaService->darDeBajaPorCausaId($causa->id);
            }

            DB::commit();
            return response()->json([
                'message' => MessageHttp::ACTUALIZADO_CORRECTAMENTE,
                'data' => $causa
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error actualizado causa: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error actualizado causa',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Causa $causa)
    {
        if ($this->causaService->tieneOrdenesNoCerradas($causa->id)) {
            return response()->json([
                'message' => 'No se puede eliminar la causa porque tiene órdenes no cerradas.',
                'data' => null
            ], 409);
        }
        //Elimina la causa de PaqueteCausa (si esxistiera)
        $this->paqueteCausaService->darDeBajaPorCausaId($causa->id);

        $causa->es_eliminado = 1;
        $causa->save();
        $data = [
            'message' => MessageHttp::ELIMINADO_CORRECTAMENTE,
            'data' => $causa
        ];
        return response()->json($data);
    }
    public function guardarCausaPosta($causaId, $pantillaId)
    {
        $avancePlantilla = $this->avancePlantillaService->obtenerUno($pantillaId);
        //Causa Posta cero, (INICIO)
        $dataCausaPostaInicio = [
            'nombre' => 'INICIO',
            'numero_posta' => 0,
            'copia_nombre_plantilla' => $avancePlantilla->nombre,
            'tiene_informe' => 0,
            'causa_id' => $causaId,
        ];
        $causaPosta = $this->causaPostaService->store($dataCausaPostaInicio);

        $postas = $this->postaService->listarPorAvancePlantillaId($avancePlantilla->id);
        foreach ($postas as $posta) {
            $data = [
                'nombre' => $posta->nombre,
                'numero_posta' => $posta->numero_posta,
                'copia_nombre_plantilla' => $avancePlantilla->nombre,
                'tiene_informe' => 0,
                'causa_id' => $causaId,
            ];
            // Llama a la función store de CausaPostaService
            $causaPosta = $this->causaPostaService->store($data);
        }
        return $causaPosta;
    }
    public function listarCausasParaPaquete()
    {
        $causas = $this->causaService->listarCausasParaPaquete();
        $data = [
            'message' => MessageHttp::OBTENIDOS_CORRECTAMENTE,
            'data' => $causas
        ];
        return response()->json($data);
    }
    //Listado de causas para seguimiento de ordenes
    public function listadoCausasOrdenGiradas(Request $request)
    {
        try {
            $causas = $this->listadoCausasOrdenGiradaService->devuelveListadoCausasOrdenGirada($request);
            return $causas;
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las causas.',
                'data' => null
            ], 500);
        }
    }
    public function listadoCausasOrdenPrePresupuestadas(Request $request)
    {
        try {
            $causas = $this->listadoCausasOrdenPrePresupuestadasService->devuelveListadoCausasOrdenPrePresupuestadas($request);
            return $causas;
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las causas.',
                'data' => null
            ], 500);
        }
    }
    public function listadoCausasOrdenPresupuestadas(Request $request)
    {
        try {
            $causas = $this->listadoCausasOrdenPresupuestadasService->devuelveListadoCausasOrdenPresupuestadas($request);
            return $causas;
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las causas.',
                'data' => null
            ], 500);
        }
    }
    public function listadoCausasOrdenAceptadas(Request $request)
    {
        try {
            $causas = $this->listadoCausasOrdenAceptadasService->devuelveListadoCausasOrdenAceptadas($request);
            return $causas;
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las causas.',
                'data' => null
            ], 500);
        }
    }
    public function listadoCausasOrdenDineroEntregado(Request $request)
    {
        try {
            $causas = $this->listadoCausasOrdenDineroEntregadoService->devuelveListadoCausasOrdenDineroEntregado($request);
            return $causas;
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las causas.',
                'data' => null
            ], 500);
        }
    }
    public function listadoCausasOrdenListaRealizar(Request $request)
    {
        try {
            $causas = $this->listadoCausasOrdenListaRealizarService->devuelveListadoCausasOrdenListaRealizar($request);
            return $causas;
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las causas.',
                'data' => null
            ], 500);
        }
    }
    public function listadoCausasOrdenDescargadas(Request $request)
    {
        try {
            $causas = $this->listadoCausasOrdenDescargadaService->devuelveListadoCausasOrdenDescargada($request);
            return $causas;
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las causas.',
                'data' => null
            ], 500);
        }
    }
    public function listadoCausasOrdenPronuncioAbogado(Request $request)
    {
        try {
            $causas = $this->listadoCausasOrdenPronuncioAbogadoService->devuelveListadoCausasOrdenPronuncioAbogado($request);
            return $causas;
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las causas.',
                'data' => null
            ], 500);
        }
    }
    public function listadoCausasOrdenCuentasConciliadas(Request $request)
    {
        try {
            $causas = $this->listadoCausasOrdenCuentasConciliadaService->devuelveListadoCausasOrdenCuentasConciliadas($request);
            return $causas;
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las causas.',
                'data' => null
            ], 500);
        }
    }
    public function listadoCausasOrdenVencidasLeves(Request $request)
    {
        try {
            $causas = $this->listadoCausasOrdenVencidasLevesService->devuelveListadoCausasOrdenVencidasLeves($request);
            return $causas;
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las causas.',
                'data' => null
            ], 500);
        }
    }
    public function listadoCausasOrdenVencidasGraves(Request $request)
    {
        try {
            $causas = $this->listadoCausasOrdenVencidasGravesService->devuelveListadoCausasOrdenVencidasGraves($request);
            return $causas;
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las causas.',
                'data' => null
            ], 500);
        }
    }
    public function causasConBilletera(Request $request)
    {
        $query = Causa::where('estado', '!=', EstadoCausa::TERMINADA)
            ->where('es_eliminado', 0)
            ->where('tiene_billetera', 1);

        $usuario = Auth::user();
        //Filtrado por usuario
        if ($usuario->tipo === TipoUsuario::ABOGADO_LIDER || $usuario->tipo === TipoUsuario::ABOGADO_INDEPENDIENTE) {
            $query->where('usuario_id', $usuario->id);
        }


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
    public function listarCausasConBilletera()
    {
        $causas = $this->causaService->listarCausasConBilletera();
        $data = [
            'message' => MessageHttp::OBTENIDOS_CORRECTAMENTE,
            'data' => $causas
        ];
        return response()->json($data);
    }
    public function listarCausasDestinoTransaccion()
    {
        $causas = $this->causaService->listarCausasDestinoTransaccion();
        $data = [
            'message' => MessageHttp::OBTENIDOS_CORRECTAMENTE,
            'data' => $causas
        ];
        return response()->json($data);
    }
    public function listaCausasConBilleteraUsuario()
    {
        $causas = $this->causaService->listadoDetallesCausasConBilleterasDeUsuario();
        $data = [
            'message' => MessageHttp::OBTENIDOS_CORRECTAMENTE,
            'data' => $causas
        ];
        return response()->json($data);
    }
    //Listados de causas para seguimiento, para el usuario Lider
    public function listadoCausasOrdenGiradasDeLider(Request $request)
    {
        try {
            $causas = $this->listadoCausasSeguimientoLiderService->listarCausasConOrdenesGiradasDeLider($request);
            return $causas;
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las causas.',
                'data' => null
            ], 500);
        }
    }
    public function listadoCausasOrdenPrePresupuestadasDeLider(Request $request)
    {
        try {
            $causas = $this->listadoCausasSeguimientoLiderService->listarCausasConOrdenesPrePresupuestadasDeLider($request);
            return $causas;
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las causas.',
                'data' => null
            ], 500);
        }
    }
    public function listadoCausasOrdenPresupuestadasDeLider(Request $request)
    {
        try {
            $causas = $this->listadoCausasSeguimientoLiderService->listarCausasConOrdenesPresupuesDeLider($request);
            return $causas;
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las causas.',
                'data' => null
            ], 500);
        }
    }
    public function listadoCausasOrdenAceptadasDeLider(Request $request)
    {
        try {
            $causas = $this->listadoCausasSeguimientoLiderService->listarCausasConOrdenesAceptadasDeLider($request);
            return $causas;
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las causas.',
                'data' => null
            ], 500);
        }
    }
    public function listadoCausasOrdenDineroEntregadoDeLider(Request $request)
    {
        try {
            $causas = $this->listadoCausasSeguimientoLiderService->listarCausasConOrdenesDineroEntregadoDeLider($request);
            return $causas;
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las causas.',
                'data' => null
            ], 500);
        }
    }
    public function listadoCausasOrdenListaRealizarDeLider(Request $request)
    {
        try {
            $causas = $this->listadoCausasSeguimientoLiderService->listarCausasConOrdenesListaRealizarDeLider($request);
            return $causas;
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las causas.',
                'data' => null
            ], 500);
        }
    }
    public function listadoCausasOrdenDescargadasDeLider(Request $request)
    {
        try {
            $causas = $this->listadoCausasSeguimientoLiderService->listarCausasConOrdenesDescargadasDeLider($request);
            return $causas;
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las causas.',
                'data' => null
            ], 500);
        }
    }
    public function listadoCausasOrdenPronuncioAbogadoDeLider(Request $request)
    {
        try {
            $causas = $this->listadoCausasSeguimientoLiderService->listarCausasConOrdenesPronuncioAbogadoDeLider($request);
            return $causas;
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las causas.',
                'data' => null
            ], 500);
        }
    }
    public function listadoCausasOrdenCuentasConciliadasDeLider(Request $request)
    {
        try {
            $causas = $this->listadoCausasSeguimientoLiderService->listarCausasConOrdenesCuentasConciliadasDeLider($request);
            return $causas;
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las causas.',
                'data' => null
            ], 500);
        }
    }
    public function listadoCausasOrdenVencidasLevesDeLider(Request $request)
    {
        try {
            $causas = $this->listadoCausasSeguimientoLiderService->listarCausasConOrdenesVencidasLevesDeLider($request);
            return $causas;
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las causas.',
                'data' => null
            ], 500);
        }
    }
    public function listadoCausasOrdenVencidasGravesDeLider(Request $request)
    {
        try {
            $causas = $this->listadoCausasSeguimientoLiderService->listarCausasConOrdenesVencidasGravesDeLider($request);
            return $causas;
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las causas.',
                'data' => null
            ], 500);
        }
    }
    public function dineroComprometidoCausa($causaId)
    {
        $totales = $this->causaService->obtenerDineroComprometidoCausa($causaId);

        return response()->json([
            'message' => MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data' => $totales
        ], 200);
    }
    public function dineroComprometidoGeneral()
    {
        $idUser = Auth::user()->id;
        $totales = $this->causaService->obtenerTotalComprometidoSinBilletera($idUser);

        return response()->json([
            'message' => MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data' => $totales
        ], 200);
    }
    public function indexTodas(Request $request)
    {
        $query = Causa::where('es_eliminado', 0);

        $usuario = Auth::user();
        //Filtrado por usuario
        if ($usuario->tipo === TipoUsuario::ABOGADO_LIDER || $usuario->tipo === TipoUsuario::ABOGADO_INDEPENDIENTE) {
            $query->where('usuario_id', $usuario->id);
        } else {
            if ($usuario->tipo === TipoUsuario::ABOGADO_DEPENDIENTE) {
                $query->where('abogado_id', $usuario->id);
            } else {
                if ($usuario->tipo === TipoUsuario::PROCURADOR) {
                    $query->where('procurador_id', $usuario->id);
                }
            }
        }


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
    public function listadoCausasActivasConBilleteras()
    {
        $causas = $this->causaService->listadoCausasActivasConBilleteras();
        return response()->json([
            'message' => MessageHttp::OBTENIDOS_CORRECTAMENTE,
            'data' => $causas
        ], 200);
    }
    public function listadoCausasTerminadasConBilleteras()
    {
        $causas = $this->causaService->listadoCausasTerminadasConBilleteras();
        return response()->json([
            'message' => MessageHttp::OBTENIDOS_CORRECTAMENTE,
            'data' => $causas
        ], 200);
    }
    public function obtenerNombreTribunalDominante($causaId)
    {
        $causa = Causa::with(['tribunales.claseTribunal'])
            ->find($causaId);

        if (!$causa) {
            return [
                'procurador_id' => null,
                'tribunal_dominante' => null,
            ];
        }

        $tribunalDominante = $causa->tribunales
            ->firstWhere('tribunal_dominante', 1);

        return [
            'procurador_id' => $causa->procurador_id,
            'tribunal_dominante' => $tribunalDominante?->claseTribunal?->nombre,
        ];
    }
}
