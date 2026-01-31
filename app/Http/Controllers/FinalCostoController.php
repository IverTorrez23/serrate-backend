<?php

namespace App\Http\Controllers;

use Exception;
use App\Http\Requests\CostoJudicialVentaRequest;
use App\Models\Billetera;
use App\Models\BilleteraTransaccion;
use App\Models\Causa;
use App\Models\FinalCosto;
use App\Models\Orden;
use App\Services\BilleteraService;
use App\Services\BilleteraTransaccionService;
use App\Services\CausaService;
use App\Services\FinalCostoService;
use App\Services\TransaccionesCausaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FinalCostoController extends Controller
{
    protected $finalCostoService;
    protected $causaService;
    protected $billeteraTransaccionService;
    protected $billeteraService;
    protected $transaccionesCausaService;

    public function __construct(FinalCostoService $finalCostoService, CausaService $causaService, BilleteraTransaccionService $billeteraTransaccionService, BilleteraService $billeteraService, TransaccionesCausaService $transaccionesCausaService)
    {
        $this->finalCostoService = $finalCostoService;
        $this->causaService = $causaService;
        $this->billeteraTransaccionService = $billeteraTransaccionService;
        $this->billeteraService = $billeteraService;
        $this->transaccionesCausaService = $transaccionesCausaService;
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
    public function show(FinalCosto $finalCosto)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FinalCosto $finalCosto)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FinalCosto $finalCosto)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FinalCosto $finalCosto)
    {
        //
    }
    public function colocarCostoJudicialVenta(CostoJudicialVentaRequest $request, FinalCosto $finalCosto)
    {
        if ($finalCosto->es_validado === 1) {
            return response()->json([
                'message' => 'Error, esta orden ya se coloco el costo judicial venta.',
                'data' => null
            ], 409);
        }
        //Evaluacion EAP
        $orden = Orden::findOrFail($finalCosto->orden_id);
        $montoProbable = $request->costo_procesal_venta - $finalCosto->costo_procesal_compra;
        if ($this->causaService->noPasoValidacionEAPECausa($orden->causa_id, $montoProbable)) {
            return response()->json([
                'message' => 'ALERTA!
     Su solicitud no puede concretarse por falta de saldo en la billetera. Por favor, agregue saldo y luego vuelva a intentarlo.',
                'data' => null
            ], 409);
        }

        DB::beginTransaction();
        try {
            $costoProcesalVenta = $request->costo_procesal_venta;
            $totalEgreso = $costoProcesalVenta + $finalCosto->costo_procuraduria_venta;
            $gananciaProcesal = $costoProcesalVenta - $finalCosto->costo_procesal_compra;
            $dataFinalCosto = [
                'costo_procesal_venta' => $costoProcesalVenta,
                'total_egreso' => $totalEgreso,
                'ganancia_procesal' => $gananciaProcesal,
                'es_validado' => 1
            ];

            $finalCosto = $this->finalCostoService->update($dataFinalCosto, $finalCosto->id);
            //Actualizacion de billetera general o independiente, dependiendo
            //Si coloca costo procesal venta mayor al costo procesal compra
            if ($montoProbable > 0) {
                $causa = Causa::findOrFail($orden->causa_id);
                if ($causa->tiene_billetera === 1) {
                    //Actualizacion de transaccion de causa
                    $transaccionesCausa = $this->transaccionesCausaService->obtenerPorOrdenId($orden->id);
                    $dataTrnCausa = [
                        'monto' => $finalCosto->total_egreso
                    ];
                    $transaccionesCausa = $this->transaccionesCausaService->update($dataTrnCausa, $transaccionesCausa->id);
                    //Actualizacion del saldo de billetera de causa
                    $nuevoSaldoBilleteraCausa = $causa->billetera - $montoProbable;
                    $dataCausa = [
                        'billetera' => $nuevoSaldoBilleteraCausa
                    ];
                    $causa = $this->causaService->update($dataCausa, $causa->id);
                } else { //Por falso, actualiza la billetera general y la transaccion de billetera general
                    //Actualizacion de transacciones de billetera general
                    $billteraTransaccion = $this->billeteraTransaccionService->obtenerPorOrdenId($orden->id);
                    $dataBilleteraTransaccion = [
                        'monto' => $finalCosto->total_egreso
                    ];
                    $billteraTransaccion = $this->billeteraTransaccionService->update($dataBilleteraTransaccion, $billteraTransaccion->id);
                    //Actualizacion de billetera general
                    $billetera = Billetera::findOrFail($billteraTransaccion->billetera_id);

                    $nuevoSaldoBilletera = $billetera->monto - $montoProbable;
                    $dataBilletera = [
                        'monto' => $nuevoSaldoBilletera
                    ];
                    $billetera = $this->billeteraService->update($dataBilletera, $billetera->id);
                }
            }
            DB::commit();
            return response()->json([
                'message' => 'Costo Judicial venta registrado correctamente',
                'data' => $finalCosto
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al colocar costo judicia venta: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error al colocar costo judicia venta, intente nuevamente',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
