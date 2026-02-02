<?php

namespace App\Http\Controllers;

use Exception;
use App\Constants\FechaHelper;
use App\Constants\GlosaTransaccion;
use App\Constants\TipoTransaccion;
use App\Constants\Transaccion;
use App\Enums\MessageHttp;
use App\Http\Requests\StoreRetiroRequest;
use App\Http\Resources\TransaccionesCausaCollection;
use App\Models\Retiro;
use App\Models\TablaConfig;
use App\Services\RetiroService;
use App\Services\TransaccionesAdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class RetiroController extends Controller
{
    protected $retiroService;
    protected $transaccionesAdminService;

    public function __construct(RetiroService $retiroService, TransaccionesAdminService $transaccionesAdminService)
    {
        $this->retiroService = $retiroService;
        $this->transaccionesAdminService = $transaccionesAdminService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $data = $this->retiroService->obtenerRetiros($request);
            return new TransaccionesCausaCollection($data);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener los retiros.',
                'data' => null
            ], 500);
        }
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
    public function store(StoreRetiroRequest $request)
    {
        $monto = $request->monto;
        $glosa = $request->glosa;
        $fechaHora = FechaHelper::fechaHoraBolivia();
        $tablaConfig = TablaConfig::findOrFail(1);
        if ($monto > $tablaConfig->caja_admin) {
            return response()->json([
                'message' => 'Usted no tiene suficiente saldo para realizar esta acción',
                'data' => null
            ], 409);
        }
        DB::beginTransaction();
        try {
            $dataRetiro = [
                'monto' => $monto,
                'fecha_retiro' => $fechaHora,
                'glosa' => $glosa,
                'usuario_id' => Auth::user()->id
            ];
            $retiro = $this->retiroService->store($dataRetiro);

            $dataTrnAdmin = [
                'monto' => $monto,
                'fecha_transaccion' => $fechaHora,
                'tipo' => TipoTransaccion::DEBITO,
                'transaccion' => Transaccion::EGRESO_POR_RETIRO,
                'glosa' => GlosaTransaccion::DEBITO_POR_RETIRO,
                'usuario_id' => Auth::user()->id,
                'billetera_id' => 0
            ];
            $transaccionesAdmin = $this->transaccionesAdminService->registrarTransaccionAdmin($dataTrnAdmin);

            DB::commit();
            return response()->json([
                'message' => MessageHttp::CREADO_CORRECTAMENTE,
                'data' => $retiro
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error registrar retiro: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error registrar retiro',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Retiro $retiro)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Retiro $retiro)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Retiro $retiro)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Retiro $retiro)
    {
        //
    }
}
