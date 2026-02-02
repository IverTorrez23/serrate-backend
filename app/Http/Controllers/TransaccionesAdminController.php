<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Enums\MessageHttp;
use App\Constants\GlosaTransaccion;
use App\Constants\TipoTransaccion;
use App\Constants\Transaccion;
use App\Http\Requests\StoreDepositoAContadorRequest;
use App\Http\Resources\TransaccionesCausaCollection;
use App\Models\TablaConfig;
use App\Models\TransaccionesAdmin;
use App\Services\TransaccionesAdminService;
use App\Services\TransaccionesContadorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class TransaccionesAdminController extends Controller
{
    protected $transaccionesAdminService;
    protected $transaccionesContadorService;

    public function __construct(TransaccionesAdminService $transaccionesAdminService, TransaccionesContadorService $transaccionesContadorService)
    {
        $this->transaccionesAdminService = $transaccionesAdminService;
        $this->transaccionesContadorService = $transaccionesContadorService;
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
    public function show(TransaccionesAdmin $transaccionesAdmin)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransaccionesAdmin $transaccionesAdmin)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TransaccionesAdmin $transaccionesAdmin)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransaccionesAdmin $transaccionesAdmin)
    {
        //
    }
    public function obtenerTransaccionesDeAdmin(Request $request)
    {
        try {
            $data = $this->transaccionesAdminService->obtenerTransaccionesDeAdmin($request);
            return new TransaccionesCausaCollection($data);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las transacciones de admin.',
                'data' => null
            ], 500);
        }
    }
    public function depositoAContador(StoreDepositoAContadorRequest $request)
    {
        $fechaHora = Carbon::now('America/La_Paz')->toDateTimeString();
        $monto = $request->monto;
        $tablaConfig = TablaConfig::findOrFail(1);
        if ($tablaConfig->caja_admin < $monto) {
            return response()->json([
                'message' => 'Usted no tiene suficiente saldo para realizar esta acción',
                'data' => null
            ], 409);
        }
        DB::beginTransaction();
        try {
            $dataTrnAdmin = [
                'monto' => $monto,
                'fecha_transaccion' => $fechaHora,
                'tipo' => TipoTransaccion::DEBITO,
                'transaccion' => Transaccion::EGRESO_POR_DEPOSITO_CONTADOR,
                'glosa' => GlosaTransaccion::DEBITO_POR_DEPOSITO_CONTADOR,
                'usuario_id' => Auth::user()->id,
                'billetera_id' => 0
            ];
            $transaccionesAdmin = $this->transaccionesAdminService->registrarTransaccionAdmin($dataTrnAdmin);

            $dataTrnContador = [
                'monto' => $monto,
                'fecha_transaccion' => $fechaHora,
                'tipo' => TipoTransaccion::CREDITO,
                'transaccion' => Transaccion::INGRESO_POR_DEPOSITO_ADMIN,
                'glosa' => GlosaTransaccion::CREDITO_POR_DEPOSITO_ADMIN,
                'contador_id' => 0,
                'usuario_id' => Auth::user()->id
            ];
            $transaccionContador = $this->transaccionesContadorService->registrarTransaccionContador($dataTrnContador);

            DB::commit();
            return response()->json([
                'message' => MessageHttp::CREADO_CORRECTAMENTE,
                'data' => $transaccionesAdmin
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
