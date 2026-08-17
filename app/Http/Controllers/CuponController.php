<?php

namespace App\Http\Controllers;

use App\Constants\Estado;
use App\Constants\EstadoCupon;
use App\Constants\FechaHelper;
use App\Enums\MessageHttp;
use App\Http\Requests\StoreCuponRequest;
use App\Http\Resources\DistritoCollection;
use App\Models\Cupon;
use App\Models\Paquete;
use App\Services\CompraPaqueteService;
use App\Services\CuponService;
use App\Services\PaqueteService;
use App\Services\ResponseService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CuponController extends Controller
{
    protected CuponService $cuponService;
    protected PaqueteService $paqueteService;
    protected CompraPaqueteService $compraPaqueteService;
    public function __construct(CuponService $cuponService, PaqueteService $paqueteService, CompraPaqueteService $compraPaqueteService)
    {
        $this->cuponService = $cuponService;
        $this->paqueteService = $paqueteService;
        $this->compraPaqueteService = $compraPaqueteService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Cupon::active();

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
        $cupones = $query->paginate($perPage);

        return new DistritoCollection($cupones);
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
    public function store(StoreCuponRequest $request)
    {
        try {
            $codigoGenerado = $this->cuponService->generarCodigoUnico();
            $data = [
                'codigo' => $codigoGenerado,
                'paquete_id' => $request->paquete_id,
                'fecha_uso' => null,
                'usuario_id' => 0,
                'estado' => Estado::ACTIVO,
                'es_eliminado' => 0
            ];

            $cupon = $this->cuponService->store($data);

            return ResponseService::success(
                message: MessageHttp::CREADO_CORRECTAMENTE,
                data: $cupon
            );
        } catch (\Throwable $e) {

            Log::error('Error al registrar cupón', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request' => $request->all()
            ]);

            return ResponseService::error(
                config('app.debug')
                    ? $e->getMessage()
                    : 'No se pudo registrar el cupón.',
                500
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Cupon $cupon)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cupon $cupon)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cupon $cupon)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cupon $cupon)
    {
        DB::beginTransaction();
        try {
            $cupon = $this->cuponService->destroy($cupon);

            DB::commit();
            return response()->json([
                'message' => MessageHttp::ELIMINADO_CORRECTAMENTE,
                'data' => $cupon
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error eliminar cupon: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error interno al eliminar cupon',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function listadoPorPaquete(Request $request, $paqueteId)
    {
        $query = Cupon::with([
            'paquete:id,nombre',
            'usuario:id',
            'usuario.persona:id,usuario_id,nombre,apellido'
        ])
            ->where('paquete_id', $paqueteId)
            ->whereIn('estado', [
                EstadoCupon::ACTIVO,
                EstadoCupon::USADO
            ])
            ->where('es_eliminado', 0);

        if ($request->has('search')) {
            $search = json_decode($request->input('search'), true);
            $query->search($search);
        }

        if ($request->has('sort')) {
            $sort = json_decode($request->input('sort'), true);
            $query->sort($sort);
        }

        $perPage = $request->input('perPage', 10);

        $cupones = $query->paginate($perPage);

        return new DistritoCollection($cupones);
    }
    public function storeLote(StoreCuponRequest $request): JsonResponse
    {
        try {
            $paquete = Paquete::query()
                ->select([
                    'id',
                    'nombre',
                    'fecha_limite_compra',
                    'cantidad_dias',
                    'precio',
                ])
                ->findOrFail($request->integer('paquete_id'));

            $cantidad = $request->integer('cantidad');

            $cuponesGenerados = DB::transaction(
                function () use ($paquete, $cantidad) {
                    $cupones = [];

                    for ($i = 0; $i < $cantidad; $i++) {
                        $codigoGenerado = $this
                            ->cuponService
                            ->generarCodigoUnico();

                        $cupon = $this->cuponService->store([
                            'codigo' => $codigoGenerado,
                            'paquete_id' => $paquete->id,
                            'fecha_uso' => null,
                            'usuario_id' => 0,
                            'estado' => Estado::ACTIVO,
                            'es_eliminado' => 0,
                        ]);

                        $cupones[] = [
                            'id' => $cupon->id,
                            'codigo' => $cupon->codigo,
                            'fecha_limite_activacion' =>
                            $paquete->fecha_limite_compra,
                            'vigencia_dias' => $paquete->cantidad_dias,
                            'precio' => (float) $paquete->precio,
                            'nombre_paquete' => $paquete->nombre,
                        ];
                    }

                    return $cupones;
                }
            );

            return response()->json([
                'status' => 'success',
                'message' => "{$cantidad} cupones generados correctamente.",
                'data' => [
                    'paquete' => [
                        'id' => $paquete->id,
                        'nombre' => $paquete->nombre,
                        'fecha_limite_compra' =>
                        $paquete->fecha_limite_compra,
                        'cantidad_dias' => $paquete->cantidad_dias,
                        'precio' => (float) $paquete->precio,
                    ],
                    'cantidad_generada' => count($cuponesGenerados),
                    'cupones' => $cuponesGenerados,
                ],
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Error al generar cupones por lote', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'No se pudieron generar los cupones.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null,
            ], 500);
        }
    }

    public function canjear(Request $request): JsonResponse
    {
        $request->validate([
            'codigo' => [
                'required',
                'string',
                'max:20',
            ],
            'paquete_id' => [
                'required',
                'integer',
                'exists:paquetes,id',
            ],
        ]);
        DB::beginTransaction();
        try {
            $usuario = auth()->user();

            if (!$usuario) {
                return ResponseService::error(
                    'Debe iniciar sesión para canjear el cupón.',
                    401
                );
            }
            //validacion de tipo de usuario con tipo de paquete

            $paquete = $this->paqueteService->obtenerUno($request->integer('paquete_id'));
            if ($usuario->tipo !== $paquete->tipo) {
                $tipoPaquete = str_replace('_', ' ', $paquete->tipo);
                $tipoUsuario = str_replace('_', ' ', $usuario->tipo);
                return ResponseService::error(
                    'El paquete no corresponde a su tipo de usuario. '
                        . 'Este paquete es para un usuario tipo '
                        . $tipoPaquete
                        . ', y usted es '
                        . $tipoUsuario
                        . '.',
                    403
                );
            }
            if ($paquete->tiene_fecha_limite === 1) {
                if (FechaHelper::soloFechaBolivia() > $paquete->fecha_limite_compra) {
                    return ResponseService::error(
                        'Este paquete ya vencio su fecha limite de canjeo, feche limite de canjeo :' . $paquete->fecha_limite_compra,
                        401
                    );
                }
            }



            $cupon = $this->cuponService->canjearCupon(
                strtoupper(trim($request->codigo)),
                $request->integer('paquete_id'),
                $usuario->id
            );

            $compraPaquete = $this->compraPaqueteService->registroCompraPaqueteConCupon($usuario->id, $paquete->cantidad_dias, $paquete->id, $paquete->precio);
            DB::commit();
            return ResponseService::success(
                message: 'Cupón canjeado correctamente.',
                data: $cupon
            );
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return ResponseService::error(
                $e->getMessage(),
                404
            );
        } catch (\RuntimeException $e) {

            return ResponseService::error(
                $e->getMessage(),
                422
            );
        } catch (\Throwable $e) {

            Log::error('Error al canjear cupón', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'codigo' => $request->codigo,
                'paquete_id' => $request->paquete_id,
                'usuario_id' => auth()->id(),
            ]);

            return ResponseService::error(
                config('app.debug')
                    ? $e->getMessage()
                    : 'No se pudo canjear el cupón.',
                500
            );
        }
    }
}
