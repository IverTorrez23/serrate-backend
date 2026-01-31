<?php

namespace App\Http\Controllers;

use App\Constants\TipoUsuario;
use App\Http\Requests\StorePaqueteRequest;
use App\Http\Requests\UpdatePaqueteRequest;
use App\Http\Resources\PaqueteCollection;
use App\Models\Paquete;
use App\Services\PaqueteService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Enums\MessageHttp;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\Log;

class PaqueteController extends Controller
{
    protected $paqueteService;

    public function __construct(
        PaqueteService $paqueteService
    ) {
        $this->paqueteService = $paqueteService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Paquete::active();
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
        $paquetes = $query->paginate($perPage);
        return new PaqueteCollection($paquetes);
    }

    public function listadoPaquetes()
    {
        $paquetes = $this->paqueteService->listadoPaquetes();
        return response()->json([
            'message' => MessageHttp::OBTENIDOS_CORRECTAMENTE,
            'data' => $paquetes
        ]);
    }
    public function listadoPaquetesSegunUsuario()
    {
        $tipoUser = Auth::user()->tipo;
        if ($tipoUser === TipoUsuario::ABOGADO_LIDER) {
            $paquetes = $this->paqueteService->listadoPaquetesParaLider();
        }
        if ($tipoUser === TipoUsuario::ABOGADO_INDEPENDIENTE) {
            $paquetes = $this->paqueteService->listadoPaquetesParaIndependiente();
        }
        return response()->json([
            'message' => MessageHttp::OBTENIDOS_CORRECTAMENTE,
            'data' => $paquetes
        ]);
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
    public function store(StorePaqueteRequest $request)
    {
        $fechaHora = Carbon::now('America/La_Paz')->toDateTimeString();
        $data = [
            'nombre' => $request->nombre,
            'precio' => $request->precio,
            'cantidad_dias' => $request->cantidad_dias,
            'descripcion' => $request->descripcion,
            'fecha_creacion' => $fechaHora,
            'usuario_id' => Auth::user()->id,
            'tiene_fecha_limite' => $request->tiene_fecha_limite,
            'fecha_limite_compra' => $request->fecha_limite_compra,
            'tipo' => $request->tipo
        ];
        $paquete = $this->paqueteService->store($data);
        return response()->json([
            'message' => MessageHttp::CREADO_CORRECTAMENTE,
            'data' => $paquete
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Paquete $paquete)
    {
        try {
            $paquete = $this->paqueteService->obtenerUno($paquete->id);
            return response()->json([
                'message' => MessageHttp::OBTENIDO_CORRECTAMENTE,
                'data' => $paquete
            ], 200);
        } catch (ModelNotFoundException  $e) {
            Log::error('Paquete no encontrado: ' . $e->getMessage());
            return response()->json([
                'message' => 'El paquete solicitado no se encontró.',
                'error' => 'Paquete no encontrado'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al obtener registro',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Paquete $paquete)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePaqueteRequest $request, Paquete $paquete)
    {
        $data = $request->only([
            'nombre',
            'precio',
            'cantidad_dias',
            'descripcion',
            'tiene_fecha_limite',
            'fecha_limite_compra',
            'tipo'
        ]);
        if ($request->tiene_fecha_limite === 0) {
            $data['fecha_limite_compra'] = null;
        }
        $paquete = $this->paqueteService->update($data, $paquete->id);

        return response()->json([
            'message' => MessageHttp::ACTUALIZADO_CORRECTAMENTE,
            'data' => $paquete
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Paquete $paquete)
    {
        $paquete = $this->paqueteService->destroy($paquete);

        return response()->json([
            'message' => MessageHttp::ELIMINADO_CORRECTAMENTE,
            'data' => $paquete
        ], 200);
    }
}
