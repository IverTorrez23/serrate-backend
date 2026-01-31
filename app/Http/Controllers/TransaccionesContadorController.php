<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Enums\MessageHttp;
use App\Constants\GlosaTransaccion;
use App\Constants\TipoTransaccion;
use App\Constants\Transaccion;
use App\Http\Requests\StoreDepositoAContadorRequest;
use App\Models\TablaConfig;
use App\Http\Resources\TransaccionesCausaCollection;
use App\Models\TransaccionesContador;
use App\Services\TransaccionesAdminService;
use App\Services\TransaccionesContadorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class TransaccionesContadorController extends Controller
{
    protected $transaccionesContadorService;
    protected $transaccionesAdminService;

    public function __construct(TransaccionesContadorService $transaccionesContadorService, TransaccionesAdminService $transaccionesAdminService)
    {
        $this->transaccionesContadorService = $transaccionesContadorService;
        $this->transaccionesAdminService = $transaccionesAdminService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(TransaccionesContador $transaccionesContador)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransaccionesContador $transaccionesContador)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TransaccionesContador $transaccionesContador)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransaccionesContador $transaccionesContador)
    {
        //
    }
    public function obtenerTransaccionesDeContador(Request $request)
    {
        try {
            $data = $this->transaccionesContadorService->obtenerTransaccionesContador($request);
            return new TransaccionesCausaCollection($data);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las transacciones de contador.',
                'data' => null
            ], 500);
        }
    }
    public function devolucionAAdmin(StoreDepositoAContadorRequest $request)
    {
        $fechaHora = Carbon::now('America/La_Paz')->toDateTimeString();
        $monto = $request->monto;
        $tablaConfig = TablaConfig::findOrFail(1);
        if ($tablaConfig->caja_contador < $monto) {
            return response()->json([
                'message' => 'Usted no tiene suficiente saldo para realizar esta acción',
                'data' => null
            ], 409);
        }
        DB::beginTransaction();
        try {
            $dataTrnContador = [
                'monto' => $monto,
                'fecha_transaccion' => $fechaHora,
                'tipo' => TipoTransaccion::DEBITO,
                'transaccion' => Transaccion::EGRESO_POR_DEVOLUCION_ADMIN,
                'glosa' => GlosaTransaccion::EGRESO_POR_DEVOLUCION_ADMIN,
                'contador_id' => Auth::user()->id,
                'usuario_id' => Auth::user()->id
            ];
            $transaccionContador = $this->transaccionesContadorService->registrarTransaccionContador($dataTrnContador);


            $dataTrnAdmin = [
                'monto' => $monto,
                'fecha_transaccion' => $fechaHora,
                'tipo' => TipoTransaccion::CREDITO,
                'transaccion' => Transaccion::INGRESO_POR_DEVOLUCION_CONTADOR,
                'glosa' => GlosaTransaccion::CREDITO_POR_DEVOLUCION_CONTADOR,
                'usuario_id' => Auth::user()->id
            ];
            $transaccionesAdmin = $this->transaccionesAdminService->registrarTransaccionAdmin($dataTrnAdmin);

            DB::commit();
            return response()->json([
                'message' => MessageHttp::CREADO_CORRECTAMENTE,
                'data' => $transaccionContador
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error registrar transaccion: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error registrar transaccion',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
