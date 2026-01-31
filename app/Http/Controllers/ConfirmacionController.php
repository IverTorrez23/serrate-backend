<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Orden;
use App\Models\TablaConfig;
use App\Enums\MessageHttp;
use App\Models\Confirmacion;
use Illuminate\Http\Request;
use App\Constants\EtapaOrden;
use App\Constants\GlosaTransaccion;
use App\Constants\Transaccion;
use App\Constants\TipoTransaccion;
use App\Constants\TransaccionCausa;
use App\Services\OrdenService;
use Illuminate\Support\Facades\DB;
use App\Services\CotizacionService;
use App\Services\FinalCostoService;
use Illuminate\Support\Facades\Log;
use App\Services\ConfirmacionService;
use App\Services\ProcuraduriaDescargaService;
use App\Http\Requests\UpdateConfirmacionRequest;
use App\Models\Causa;
use App\Models\ProcuraduriaDescarga;
use App\Services\BilleteraService;
use App\Services\BilleteraTransaccionService;
use App\Services\CausaService;
use App\Services\TransaccionesCausaService;
use Illuminate\Support\Facades\Auth;
use App\Services\TransaccionesContadorService;

class ConfirmacionController extends Controller
{
    protected $confirmacionService;
    protected $ordenService;
    protected $procuraduriaDescargaService;
    protected $finalCostoService;
    protected $cotizacionService;
    protected $transaccionesCausaService;
    protected $billeteraService;
    protected $billeteraTransaccionService;
    protected $causaService;
    protected $transaccionesContadorService;


    public function __construct(
        ConfirmacionService $confirmacionService,
        OrdenService $ordenService,
        ProcuraduriaDescargaService $procuraduriaDescargaService,
        FinalCostoService $finalCostoService,
        CotizacionService $cotizacionService,
        TransaccionesCausaService $transaccionesCausaService,
        BilleteraService $billeteraService,
        BilleteraTransaccionService $billeteraTransaccionService,
        CausaService $causaService,
        TransaccionesContadorService $transaccionesContadorService
    ) {
        $this->confirmacionService = $confirmacionService;
        $this->ordenService = $ordenService;
        $this->procuraduriaDescargaService = $procuraduriaDescargaService;
        $this->finalCostoService = $finalCostoService;
        $this->cotizacionService = $cotizacionService;
        $this->transaccionesCausaService = $transaccionesCausaService;
        $this->billeteraService = $billeteraService;
        $this->billeteraTransaccionService = $billeteraTransaccionService;
        $this->causaService = $causaService;
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
    public function show(Confirmacion $confirmacion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Confirmacion $confirmacion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Confirmacion $confirmacion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Confirmacion $confirmacion)
    {
        //
    }
    public function pronuncioAbogado(UpdateConfirmacionRequest $request, Confirmacion $confirmacion)
    {
        if ($confirmacion->fecha_confir_abogado) {
            return response()->json([
                'message' => 'Error, esta descarga ya fue calificada.',
                'data' => null
            ], 409);
        }
        if ($request->confir_abogado === 1 && $request->monto_propina > 0) {
            $descarga = ProcuraduriaDescarga::findOrFail($confirmacion->descarga_id);
            $orden = Orden::findOrFail($descarga->orden_id);
            //Evaluacion EAP
            if ($this->causaService->noPasoValidacionEAPECausa($orden->causa_id, $request->monto_propina)) {
                return response()->json([
                    'message' => 'ALERTA!
         Su solicitud no puede concretarse por falta de saldo en la billetera. Por favor, agregue saldo y luego vuelva a intentarlo.',
                    'data' => null
                ], 409);
            }
        }
        DB::beginTransaction();
        try {
            $fechaHora = Carbon::now('America/La_Paz')->toDateTimeString();
            $data = $request->only([
                'confir_abogado',
                'justificacion_rechazo'
            ]);

            if ($request->has('justificacion_rechazo') && $request->justificacion_rechazo === '' && $request->confir_abogado === 1) {
                $data['justificacion_rechazo'] = '';
            }
            $data['fecha_confir_abogado'] = $fechaHora;
            $confirmacion = $this->confirmacionService->update($data, $confirmacion->id);

            $descarga = $this->procuraduriaDescargaService->obtenerUno($confirmacion->descarga_id);
            //Verificacion si hay propina para registrarlo
            if ($request->confir_abogado === 1 && $request->monto_propina > 0) {
                $dataOrden = [
                    'tiene_propina' => 1,
                    'propina' => $request->monto_propina
                ];
                $orden = $this->ordenService->update($dataOrden, $descarga->orden_id);
            }

            if ($confirmacion->fecha_confir_contador === NULL) {
                //ACTUALIZA LA ETAPA DE LA ORDEN CON PRONUNCIAMIENTO DEL ABOGADO
                $dataOrden = [
                    'etapa_orden' => EtapaOrden::PRONUNCIO_ABOGADO
                ];
                $orden = $this->ordenService->update($dataOrden, $descarga->orden_id);
            } else {
                //CIERRE DE LA ORDEN
                $calificacionOrden = ($confirmacion->confir_abogado === 1 && $confirmacion->confir_sistema === 1) ? 1 : 0;
                $ordenCerrada = $this->cerrarOrden($calificacionOrden, $descarga->orden_id);
            }


            DB::commit();
            return response()->json([
                'message' => MessageHttp::ACTUALIZADO_CORRECTAMENTE,
                'data' => $confirmacion
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error pronuncio abogado: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error pronuncio abogado',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function pronuncioContador(UpdateConfirmacionRequest $request, Confirmacion $confirmacion)
    {
        if ($confirmacion->fecha_confir_contador) {
            return response()->json([
                'message' => 'Error, esta descarga ya hizo devolucion de saldo.',
                'data' => null
            ], 409);
        }
        DB::beginTransaction();
        try {
            $fechaHora = Carbon::now('America/La_Paz')->toDateTimeString();
            /*$data = $request->only([
                'confir_contador',
            ]);*/
            $data['confir_contador'] = 1;
            $data['fecha_confir_contador'] = $fechaHora;
            $confirmacion = $this->confirmacionService->update($data, $confirmacion->id);
            //VALIDA EL CONTADOR
            $dataDescarga = [
                'es_validado' => 1
            ];
            $descarga = $this->procuraduriaDescargaService->update($dataDescarga, $confirmacion->descarga_id);
            if ($confirmacion->fecha_confir_abogado === NULL) {
                //ACTUALIZA LA ETAPA DE LA ORDEN CON PRONUNCIAMIENTO DEL CONTADOR
                $dataOrden = [
                    'etapa_orden' => EtapaOrden::PRONUNCIO_CONTADOR
                ];
                $orden = $this->ordenService->update($dataOrden, $descarga->orden_id);
            } else {
                //CIERRE DE LA ORDEN
                $calificacionOrden = ($confirmacion->confir_abogado === 1 && $confirmacion->confir_sistema === 1) ? 1 : 0;
                $ordenCerrada = $this->cerrarOrden($calificacionOrden, $descarga->orden_id);
            }

            DB::commit();
            return response()->json([
                'message' => MessageHttp::ACTUALIZADO_CORRECTAMENTE,
                'data' => $confirmacion
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error pronuncio contador: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error pronuncio contador',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function devolucionSaldoMasivo(Request $request)
    {
        $tipotrnEnDevolucion = 0;
        $now = Carbon::now('America/La_Paz');
        $fechaHora = $now->toDateTimeString();

        $confirmaciones = $request->validate([
            '*.id' => 'required|integer|exists:confirmacions,id',
            '*.descarga_id' => 'required'
        ]);
        // Validación previa: ninguno debe tener fecha_confir_contador
        foreach ($confirmaciones as $item) {
            $confir = Confirmacion::find($item['id']);
            if ($confir->fecha_confir_contador !== null) {
                $descarga = ProcuraduriaDescarga::find($confir->descarga_id);
                return response()->json([
                    'message' => "La orden {$descarga->orden_id} ya tiene devolucion registrada.",
                    'data'    => null
                ], 409);
            }
        }
        // 🔢 Obtener todos los IDs de descarga únicos
        $descargaIds = collect($confirmaciones)->pluck('descarga_id')->unique();
        // 📦 Obtener todas las descargas y sumar saldo
        $descargas = ProcuraduriaDescarga::whereIn('id', $descargaIds)->get();
        $totalSaldo = $descargas->sum('saldo');
        //Verificacion si el saldo a devolver es negativos (cuando el contador debe devolver dinero al procurador)
        if ($totalSaldo < 0) {
            $tipotrnEnDevolucion = 1; //transaccion de egreso
            $totalSaldo = $totalSaldo * (-1);
            //Validacion de caja del contador
            $tablaConfig = TablaConfig::findOrFail(1);
            if ($totalSaldo > $tablaConfig->caja_contador) {
                $totalSaldoFormateado = number_format($totalSaldo, 2, '.', ''); // como string
                return response()->json([
                    'message' => 'Usted no tiene suficiente saldo para realizar esta accion, Saldo actual = ' . $tablaConfig->caja_contador . ', saldo a devolver = ' . $totalSaldoFormateado,
                    'data' => null
                ], 409);
            }
        }

        DB::beginTransaction();

        try {
            $ordenesDevueltasPres = [];
            //Recorrido de las confirmaciones
            foreach ($confirmaciones as $item) {
                $confirmacion = Confirmacion::find($item['id']);

                $data['confir_contador'] = 1;
                $data['fecha_confir_contador'] = $fechaHora;
                $confirmacion = $this->confirmacionService->update($data, $confirmacion->id);
                //VALIDA EL CONTADOR
                $dataDescarga = [
                    'es_validado' => 1
                ];
                $descarga = $this->procuraduriaDescargaService->update($dataDescarga, $confirmacion->descarga_id);
                if ($confirmacion->fecha_confir_abogado === NULL) {
                    //ACTUALIZA LA ETAPA DE LA ORDEN CON PRONUNCIAMIENTO DEL CONTADOR
                    $dataOrden = [
                        'etapa_orden' => EtapaOrden::PRONUNCIO_CONTADOR
                    ];
                    $orden = $this->ordenService->update($dataOrden, $descarga->orden_id);
                } else {
                    //CIERRE DE LA ORDEN
                    $calificacionOrden = ($confirmacion->confir_abogado === 1 && $confirmacion->confir_sistema === 1) ? 1 : 0;
                    $ordenCerrada = $this->cerrarOrden($calificacionOrden, $descarga->orden_id);
                }
                $ordenesDevueltasPres[] = $descarga->orden_id;
            }
            /**---------------------------------------------------------------- */
            //Si hay saldo para la devolucion
            if ($totalSaldo > 0) {
                //Cuando el contador debe devolver al procurador
                if ($tipotrnEnDevolucion === 1) {
                    $dataTrnContador = [
                        'monto' => $totalSaldo,
                        'fecha_transaccion' => $fechaHora,
                        'tipo' => TipoTransaccion::DEBITO,
                        'transaccion' => Transaccion::EGRESO_POR_DEVOLUCION_PRESUPUESTO,
                        'glosa' => GlosaTransaccion::DEBITO_POR_DEVOLUCION_PRESUPUESTO . '[' . implode(',', $ordenesDevueltasPres) . ']',
                        'contador_id' => Auth::user()->id,
                        'usuario_id' => Auth::user()->id
                    ];
                } else {
                    $dataTrnContador = [
                        'monto' => $totalSaldo,
                        'fecha_transaccion' => $fechaHora,
                        'tipo' => TipoTransaccion::CREDITO,
                        'transaccion' => Transaccion::INGRESO_POR_DEVOLUCION_PRESUPUESTO,
                        'glosa' => GlosaTransaccion::CREDITO_POR_DEVOLUCION_PRESUPUESTO . '[' . implode(',', $ordenesDevueltasPres) . ']',
                        'contador_id' => Auth::user()->id,
                        'usuario_id' => Auth::user()->id
                    ];
                }
                //Registro de transaccion contador
                $transaccionContador = $this->transaccionesContadorService->registrarTransaccionContador($dataTrnContador);
            }

            DB::commit();
            return response()->json([
                'message' => 'Presupuestos devueltos correctamente',
                'data' => null
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error registrar devolucion: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error registrar devolucion',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function cerrarOrden($calificacionOrden, $ordenId)
    {
        $montoPropina = 0;
        $fechaHora = Carbon::now('America/La_Paz')->toDateTimeString();
        $calificacion = $calificacionOrden === 1 ? 'SUFICIENTE' : 'INSUFICIENTE';
        $dataOrden = [
            'etapa_orden' => EtapaOrden::CERRADA,
            'calificacion' => $calificacion,
            'fecha_cierre' => $fechaHora
        ];
        $orden = $this->ordenService->update($dataOrden, $ordenId);

        $cotizacion = $this->cotizacionService->obtenerPorIdOrden($ordenId);
        if ($orden->calificacion === 'SUFICIENTE') {
            $procuraduriaCompra = $cotizacion->compra;
            $procuraduriaVenta = $cotizacion->venta;
            $penalizacion = 0;
        } else {
            $procuraduriaCompra = 0;
            $procuraduriaVenta = 0;
            $penalizacion = $cotizacion->penalizacion;
        }
        //DATOS DE GASTO PROCESAL EN DESCARGA
        $descarga = $this->procuraduriaDescargaService->obtenerUnoPorOrdenId($ordenId);
        $totalEgreso = $descarga->compra_judicial + $procuraduriaVenta;
        $gananciaProcuraduria = $procuraduriaVenta - $procuraduriaCompra;
        $dataFinalCosto = [
            'costo_procuraduria_compra' => $procuraduriaCompra,
            'costo_procuraduria_venta' => $procuraduriaVenta,
            'costo_procesal_compra' => $descarga->compra_judicial,
            'costo_procesal_venta' => $descarga->compra_judicial,
            'total_egreso' => $totalEgreso,
            'penalidad' => $penalizacion,
            'es_validado' => 0,
            'cancelado_procurador' => 0,
            'ganancia_procuraduria' => $gananciaProcuraduria,
            'ganancia_procesal' => 0,
            'orden_id' => $ordenId,
        ];
        $finalCosto = $this->finalCostoService->store($dataFinalCosto);
        if ($orden->tiene_propina === 1) {
            $montoPropina = $orden->propina;
        }

        //Registro de transacciones en la billetera general o independiente (segun sea)
        $causa = Causa::findOrFail($orden->causa_id);
        $idUser = Auth::user()->id;
        if ($causa->tiene_billetera === 1) {
            $glosaCausa = GlosaTransaccion::DEBITO_POR_EGRESO_ORDEN . $ordenId;
            $totalEgresoTrn = $finalCosto->total_egreso + $montoPropina;
            $dataTrnCausa = [
                'monto' => $totalEgresoTrn,
                'fecha_transaccion' => $fechaHora,
                'tipo' => TipoTransaccion::DEBITO,
                'transaccion' => TransaccionCausa::EGRESO_ORDEN,
                'glosa' => $glosaCausa,
                'causa_id' => $causa->id,
                'causa_origen_destino' => 0,
                'orden_id' => $ordenId,
                'usuario_id' => $idUser,
            ];
            $transaccionCausa = $this->transaccionesCausaService->registrarTransaccionCausa($dataTrnCausa);
        } else { //Se registra transaccion en billetera general
            $codigoVisualCausa = $this->causaService->obtenerCodigoIdentificadorVisual($causa->id);
            $billetera = $this->billeteraService->obtenerUnoPorAbogadoId($causa->usuario_id);
            $billeteraId = $billetera->id;
            $monto = $finalCosto->total_egreso + $montoPropina;
            $tipoTransaccion = TipoTransaccion::DEBITO;
            $glosa = GlosaTransaccion::DEBITO_EGRESO_ORDEN_BILL_GRAL . $ordenId . ' DE LA CAUSA ' . $codigoVisualCausa;
            $ordenId = $ordenId;
            $billeteraTransaccion = $this->billeteraTransaccionService->reistroTransaccionBilletera($billeteraId, $monto, $tipoTransaccion, $glosa, $ordenId);
        }

        return $orden;
    }
}
