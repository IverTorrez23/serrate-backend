<?php

namespace App\Http\Controllers;

use App\Constants\CalificacionOrden;
use App\Constants\FechaHelper;
use App\Constants\GlosaTransaccion;
use App\Constants\TipoTransaccion;
use App\Constants\Transaccion;
use Exception;
use App\Enums\MessageHttp;
use App\Http\Requests\StoreProcuradorPagoRequest;
use App\Http\Resources\TransaccionesCausaCollection;
use App\Models\FinalCosto;
use App\Models\Orden;
use App\Models\ProcuradorPago;
use App\Services\FinalCostoService;
use App\Services\ProcuradorPagoService;
use App\Services\TablaConfigService;
use App\Services\TransaccionesAdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ProcuradorPagoController extends Controller
{
    protected $tablaConfigService;
    protected $finalCostoService;
    protected $procuradorPagoService;
    protected $transaccionesAdminService;

    public function __construct(TablaConfigService $tablaConfigService, FinalCostoService $finalCostoService, ProcuradorPagoService $procuradorPagoService, TransaccionesAdminService $transaccionesAdminService)
    {
        $this->tablaConfigService = $tablaConfigService;
        $this->finalCostoService = $finalCostoService;
        $this->procuradorPagoService = $procuradorPagoService;
        $this->transaccionesAdminService = $transaccionesAdminService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $data = $this->procuradorPagoService->obtenerPagosAProcuradores($request);
            return new TransaccionesCausaCollection($data);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener los pagos a procuradores.',
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
    public function store(StoreProcuradorPagoRequest $request)
    {
        $data = $request->validated();

        $procuradorPago = $data['procuradorPago'];
        $finalCostos = $data['finalCosto'];
        $fechaInicioConsulta = $procuradorPago['fecha_inicio_consulta'];
        $fechaFinConsulta = $procuradorPago['fecha_fin_consulta'];
        $monto = $procuradorPago['monto'];
        $procuradorId = $procuradorPago['procurador_id'];

        foreach ($finalCostos as $costo) {
            $finalCosto = FinalCosto::find($costo['id']);
            if ($finalCosto->cancelado_procurador === 1) {
                return response()->json([
                    'message' => "La orden {$finalCosto->orden_id} ya se cancelo al procurador.",
                    'data'    => null
                ], 409);
            }
        }
        $tablaConfig = $this->tablaConfigService->obtenerDatos();
        if ($monto > $tablaConfig->caja_admin) {
            return response()->json([
                'message' => 'ALERTA!
                 Su solicitud no puede concretarse por saldo insuficiente',
                'data' => null
            ], 409);
        }
        DB::beginTransaction();
        try {
            $fechaHora = FechaHelper::fechaHoraBolivia();
            $ordenesCanceladas = [];
            $montoPago = 0;
            foreach ($finalCostos as $costo) {
                $finalCosto = FinalCosto::find($costo['id']);
                $orden = Orden::find($costo['orden_id']);
                if ($orden->calificacion === CalificacionOrden::SUFICIENTE) {
                    $montoPago = $montoPago + $finalCosto->costo_procuraduria_compra;
                } else {
                    $montoPago = $montoPago + $finalCosto->penalidad;
                }

                $dataFinalCosto = [
                    'cancelado_procurador' => 1
                ];
                $this->finalCostoService->update($dataFinalCosto, $finalCosto->id);
                $ordenesCanceladas[] = $orden->id;
            }

            $dataProcuradorPago = [
                'monto' => $montoPago,
                'tipo' => TipoTransaccion::DEBITO,
                'fecha_pago' => $fechaHora,
                'fecha_inicio_consulta' => $fechaInicioConsulta,
                'fecha_fin_consulta' => $fechaFinConsulta,
                'glosa' => GlosaTransaccion::PAGO_A_PROCURADOR . '[' . implode(',', $ordenesCanceladas) . ']',
                'procurador_id' => $procuradorId,
                'usuario_id' => Auth::user()->id
            ];
            $procuradorPago = $this->procuradorPagoService->store($dataProcuradorPago);
            //Reg trn en caja admin
            if ($montoPago > 0) {
                $dataTrnAdmin = [
                    'monto' => $montoPago,
                    'fecha_transaccion' => $fechaHora,
                    'tipo' => TipoTransaccion::DEBITO,
                    'transaccion' => Transaccion::EGRESO_POR_PAGO_PROCURADURIA,
                    'glosa' => GlosaTransaccion::DEBITO_POR_PAGO_PROCURADURIA . '[' . implode(',', $ordenesCanceladas) . ']',
                    'usuario_id' => Auth::user()->id
                ];
                $trnAdmin = $this->transaccionesAdminService->registrarTransaccionAdmin($dataTrnAdmin);
            } else {
                if ($montoPago < 0) {
                    $montoPago = $montoPago * (-1); //Se vuelve positivo el monto
                    $dataTrnAdmin = [
                        'monto' => $montoPago,
                        'fecha_transaccion' => $fechaHora,
                        'tipo' => TipoTransaccion::CREDITO,
                        'transaccion' => Transaccion::INGRESO_POR_PAGO_PROCURADURIA,
                        'glosa' => GlosaTransaccion::CREDITO_POR_PAGO_PROCURADURIA . '[' . implode(',', $ordenesCanceladas) . ']',
                        'usuario_id' => Auth::user()->id
                    ];
                    $trnAdmin = $this->transaccionesAdminService->registrarTransaccionAdmin($dataTrnAdmin);
                }
            }

            DB::commit();
            return response()->json([
                'message' => MessageHttp::CREADO_CORRECTAMENTE,
                'data' => $procuradorPago
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error registrar pago procurador: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error registrar pago procurador',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ProcuradorPago $procuradorPago)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProcuradorPago $procuradorPago)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProcuradorPago $procuradorPago)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProcuradorPago $procuradorPago)
    {
        //
    }
    public function obtenerUltimoPagoDeProcurador($procuradorId)
    {
        $procuradorPago = $this->procuradorPagoService->obtenerUltimoPagoDeProcurador($procuradorId);
        $data = [
            'message' => MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data' => $procuradorPago
        ];
        return response()->json($data);
    }
    public function obtenerPagosDeUnProcurador(Request $request, $procuradorId)
    {
        try {
            $data = $this->procuradorPagoService->obtenerPagosDeUnProcurador($request, $procuradorId);
            return new TransaccionesCausaCollection($data);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener los pagos de procurador.',
                'data' => null
            ], 500);
        }
    }
    public function pagoExtraordinario(StoreProcuradorPagoRequest $request)
    {
        $data = $request->validated();

        $procuradorPago = $data['procuradorPago'];
        $finalCostos = $data['finalCosto'];
        $fechaInicioConsulta = $procuradorPago['fecha_inicio_consulta'];
        $fechaFinConsulta = $procuradorPago['fecha_fin_consulta'];
        $monto = $procuradorPago['monto'];
        $procuradorId = $procuradorPago['procurador_id'];

        foreach ($finalCostos as $costo) {
            $finalCosto = FinalCosto::find($costo['id']);
            if ($finalCosto->cancelado_procurador === 1) {
                return response()->json([
                    'message' => "La orden {$finalCosto->orden_id} ya se cancelo al procurador.",
                    'data'    => null
                ], 409);
            }
        }
        // $tablaConfig = $this->tablaConfigService->obtenerDatos();
        if ($monto < 1) {
            return response()->json([
                'message' => 'ALERTA!
                 EL monto debe ser mayor a 1',
                'data' => null
            ], 409);
        }
        DB::beginTransaction();
        try {
            $fechaHora = FechaHelper::fechaHoraBolivia();
            $ordenesCanceladas = [];
            $montoPago = 0;
            foreach ($finalCostos as $costo) {
                $finalCosto = FinalCosto::find($costo['id']);
                $orden = Orden::find($costo['orden_id']);
                if ($orden->calificacion === CalificacionOrden::SUFICIENTE) {
                    $montoPago = $montoPago + $finalCosto->costo_procuraduria_compra;
                } else {
                    $montoPago = $montoPago + $finalCosto->penalidad;
                }

                $dataFinalCosto = [
                    'cancelado_procurador' => 1
                ];
                $this->finalCostoService->update($dataFinalCosto, $finalCosto->id);
                $ordenesCanceladas[] = $orden->id;
            }

            $dataProcuradorPago = [
                'monto' => $monto,
                'tipo' => TipoTransaccion::CREDITO,
                'fecha_pago' => $fechaHora,
                'fecha_inicio_consulta' => $fechaInicioConsulta,
                'fecha_fin_consulta' => $fechaFinConsulta,
                'glosa' => GlosaTransaccion::PAGO_A_PROCURADOR_EXTRAORDINARIO . '[' . implode(',', $ordenesCanceladas) . ']',
                'procurador_id' => $procuradorId,
                'usuario_id' => Auth::user()->id
            ];
            $procuradorPago = $this->procuradorPagoService->store($dataProcuradorPago);
            //Reg trn en caja admin  
            $dataTrnAdmin = [
                'monto' => $monto,
                'fecha_transaccion' => $fechaHora,
                'tipo' => TipoTransaccion::CREDITO,
                'transaccion' => Transaccion::INGRESO_POR_PAGO_PROCURADURIA,
                'glosa' => GlosaTransaccion::CREDITO_POR_PAGO_PROCURADURIA . '[' . implode(',', $ordenesCanceladas) . ']',
                'usuario_id' => Auth::user()->id
            ];
            $trnAdmin = $this->transaccionesAdminService->registrarTransaccionAdmin($dataTrnAdmin);

            DB::commit();
            return response()->json([
                'message' => MessageHttp::CREADO_CORRECTAMENTE,
                'data' => $procuradorPago
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error registrar pago procurador: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error registrar pago procurador',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
