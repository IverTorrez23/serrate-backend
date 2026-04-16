<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Constants\TipoUsuario;
use App\Constants\Estado;
use App\Enums\MessageHttp;
use App\Http\Requests\Usuarios\StoreUserRegisterRequest;
use App\Http\Resources\Usuario\UsuarioResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function crearUsuario(StoreUserRegisterRequest $request): JsonResponse
    {
        return $this->userService->crearUsuario($request->validated());
    }

    public function actualizarUsuario(Request $request, User $user): JsonResponse
    {
        return $this->userService->actualizarUsuario($user, $request->all());
    }

    public function eliminarUsuario(User $user): JsonResponse
    {
        return $this->userService->eliminarUsuario($user);
    }


    public function obtenerUsuariosDependientes(Request $request, $abogadoId)
    {
        $usuarios = $this->userService->obtenerUsuariosDependientes(
            $request,
            $abogadoId

        );
        return UsuarioResource::collection($usuarios);
    }

    public function show(User $user = null)
    {
        if ($user) {
            $data = [
                'message' => MessageHttp::OBTENIDO_CORRECTAMENTE,
                'data' => $user
            ];
        } else {
            $usuarios = $this->userService->listarActivos();
            $data = [
                'message' => MessageHttp::OBTENIDOS_CORRECTAMENTE,
                'data' => $usuarios
            ];
        }

        return response()->json($data);
    }

    public function obtenerUnPMaestro()
    {
        $usuario = User::where('tipo', TipoUsuario::PROCURADOR_MAESTRO)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->first();
        if ($usuario) {

            return $usuario;
        } else {
            return 'No se encontró ningún usuario PROCURADOR_MAESTRO.';
        }
    }

    public function listarAbogados()
    {
        $tipoUsuario = Auth::user()->tipo;
        $usuarios = '';
        if ($tipoUsuario === TipoUsuario::ABOGADO_LIDER) {
            //Lista abogados dependientes del usuario logueado
            $usuarios = $this->userService->abogadosDependientes();
        }
        if ($tipoUsuario === TipoUsuario::ADMINISTRADOR || $tipoUsuario === TipoUsuario::CONTADOR || $tipoUsuario === TipoUsuario::PROCURADOR_MAESTRO || $tipoUsuario === TipoUsuario::PROCURADOR) {
            //Todos los abogados
            $usuarios = $this->userService->listarAbogados();
        }
        $data = [
            'message' => MessageHttp::OBTENIDOS_CORRECTAMENTE,
            'data' => $usuarios
        ];
        return response()->json($data);
    }
    public function listarAbogadosDependientes($abogadoLiderId)
    {
        $usuarios = $this->userService->listarAbogadosDependientes($abogadoLiderId);
        $data = [
            'message' => MessageHttp::OBTENIDOS_CORRECTAMENTE,
            'data' => $usuarios
        ];
        return response()->json($data);
    }
    public function listarProcuradores()
    {
        $usuarios = $this->userService->listarProcuradores();
        $data = [
            'message' => MessageHttp::OBTENIDOS_CORRECTAMENTE,
            'data' => $usuarios
        ];
        return response()->json($data);
    }
    public function obtenerUnUsuario($usuarioId)
    {
        $usuario = $this->userService->obtenerUnUsuario($usuarioId);
        $data = [
            'message' => MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data' => $usuario
        ];
        return response()->json($data);
    }
    public function listaAbogadosLideres(Request $request)
    {
        $query = User::with(['persona', 'abogadosDependientes.persona'])
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('tipo', TipoUsuario::ABOGADO_LIDER);

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
        $abogadosLideres = $query->paginate($perPage);

        return UsuarioResource::collection($abogadosLideres);
    }
    public function listarAbogadosIndependientesPaginado(Request $request)
    {
        $query = User::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('tipo', TipoUsuario::ABOGADO_INDEPENDIENTE);

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
        $abogadosInd = $query->paginate($perPage);

        return UsuarioResource::collection($abogadosInd);
    }
    public function listarAbogadosProcuradoresPaginado(Request $request)
    {
        $query = User::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('tipo', TipoUsuario::PROCURADOR);

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
        $procuradores = $query->paginate($perPage);

        return UsuarioResource::collection($procuradores);
    }
    public function listarSystemUsersPaginado(Request $request)
    {
        $tiposPermitidos = [
            TipoUsuario::CONTADOR,
            TipoUsuario::PROCURADOR_MAESTRO,
            TipoUsuario::ADMINISTRADOR,
            TipoUsuario::OBSERVADOR
        ];
        $query = User::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->whereIn('tipo', $tiposPermitidos);

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
        $procuradores = $query->paginate($perPage);

        return UsuarioResource::collection($procuradores);
    }
}
