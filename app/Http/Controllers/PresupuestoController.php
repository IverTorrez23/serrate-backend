<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\TablaConfig;
use App\Constants\Estado;
use App\Enums\MessageHttp;
use App\Models\Presupuesto;
use Illuminate\Http\Request;
use App\Constants\EtapaOrden;
use App\Services\OrdenService;
use App\Services\CausaService;
use App\Services\CotizacionService;
use App\Services\PresupuestoService;
use Illuminate\Support\Facades\Auth;
use App\Services\MatrizCotizacionService;
use App\Http\Resources\PresupuestoCollection;
use App\Http\Requests\StorePresupuestoRequest;
use App\Http\Requests\UpdatePresupuestoRequest;
use App\Models\Orden;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Constants\GlosaTransaccion;
use App\Constants\TipoTransaccion;
use App\Constants\Transaccion;
use App\Services\TransaccionesContadorService;

class PresupuestoController extends Controller
{
    protected $presupuestoService;
    protected $ordenService;
    protected $matrizCotizacionService;
    protected $cotizacionService;
    protected $causaService;
    protected $transaccionesContadorService;
    public function __construct(
        PresupuestoService $presupuestoService,
        OrdenService $ordenService,
        MatrizCotizacionService $matrizCotizacionService,
        CotizacionService $cotizacionService,
        CausaService $causaService,
        TransaccionesContadorService $transaccionesContadorService
    ) {
        $this->presupuestoService = $presupuestoService;
        $this->ordenService = $ordenService;
        $this->matrizCotizacionService = $matrizCotizacionService;
        $this->cotizacionService = $cotizacionService;
        $this->causaService = $causaService;
        $this->transaccionesContadorService = $transaccionesContadorService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $presupuesto = Presupuesto::where('es_eliminado', 0)
            ->where('estado', Estado::ACTIVO)
            ->paginate();
        return new PresupuestoCollection($presupuesto);
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
    public function store(StorePresupuestoRequest $request)
    {
        $tieneRegistroPresupuesto = $this->presupuestoService->tienePresupuestoActivo($request->orden_id);
        if ($tieneRegistroPresupuesto) {
            return response()->json([
                'message' => 'Esta orden ya tiene presupuesto',
                'data' => null
            ], 409);
        }
        $orden =  Orden::findOrFail($request->orden_id);
        //Si se cambia la prioridad por otra
        $diferenciaCotizacion = 0;
        $montoTotalParaValidacion = 0;
        if ($request->prioridad < $orden->prioridad) {
            $cotizacion = $this->cotizacionService->obtenerPorIdOrden($request->orden_id);
            $matrizCotizacion = $this->matrizCotizacionService->obtenerIdDePrioridadYCondicion($request->prioridad, $cotizacion->condicion);

            $diferenciaCotizacion = $matrizCotizacion->precio_venta - $cotizacion->venta;
        }
        $montoTotalParaValidacion = $diferenciaCotizacion + $request->monto;
        if ($montoTotalParaValidacion > 0) {
            if ($this->causaService->noPasoValidacionEAPECausa($orden->causa_id, $montoTotalParaValidacion)) {
                return response()->json([
                    'message' => 'ALERTA!
                 Su solicitud no puede concretarse por falta de saldo en la billetera. Por favor, agregue saldo y luego vuelva a intentarlo.',
                    'data' => null
                ], 409);
            }
        }

        DB::beginTransaction();
        try {
            $now = Carbon::now('America/La_Paz');
            $fechaHora = $now->toDateTimeString();
            $data = [
                'monto' => $request->monto,
                'detalle_presupuesto' => $request->detalle_presupuesto,
                'fecha_presupuesto' => $fechaHora,
                'fecha_entrega' => null,
                'contador_id' => Auth::user()->id,
                'orden_id' => $request->orden_id,
            ];
            $presupuesto = $this->presupuestoService->store($data);

            //MARCA LA ORDEN PRESUPUESTADA
            $cotizacion = $this->cotizacionService->obtenerPorIdOrden($presupuesto->orden_id);
            $matrizCotizacion = $this->matrizCotizacionService->obtenerIdDePrioridadYCondicion($request->prioridad, $cotizacion->condicion);

            $dataOrden = [
                'prioridad' => $request->prioridad,
                'procurador_id' => $request->procurador_id,
                'etapa_orden' => EtapaOrden::PRESUPUESTADA,
                'matriz_id' => $matrizCotizacion->id,
            ];
            $orden = $this->ordenService->update($dataOrden, $presupuesto->orden_id);

            //ACTUALIZACION DE COTIZACION
            $dataCotizacion = [
                'compra' => $matrizCotizacion->precio_compra,
                'venta' => $matrizCotizacion->precio_venta,
                'penalizacion' => $matrizCotizacion->penalizacion,
                'prioridad' => $matrizCotizacion->numero_prioridad
            ];
            $cotizacion = $this->cotizacionService->update($dataCotizacion, $cotizacion->id);
            DB::commit();
            return response()->json([
                'message' => MessageHttp::CREADO_CORRECTAMENTE,
                'data' => $presupuesto
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error registrar descarga: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error registrar descarga',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Presupuesto $presupuesto)
    {
        $data = [
            'message' => MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data' => $presupuesto
        ];
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Presupuesto $presupuesto)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePresupuestoRequest $request, Presupuesto $presupuesto)
    {
        $data = $request->only([
            'monto',
            'detalle_presupuesto',
        ]);
        if (!$presupuesto->fecha_entrega) {
            $montoDiferenciaPresupuestoNew = 0;
            $diferenciaCotizacion = 0;
            $totalDiferenciaMonto = 0;
            //Si se aumenta en presupuesto 
            if ($request->monto > $presupuesto->monto) {
                $montoDiferenciaPresupuestoNew = $request->monto - $presupuesto->monto;
            }

            $orden =  Orden::findOrFail($presupuesto->orden_id);
            //Si se cambia la prioridad por otra
            if ($request->prioridad < $orden->prioridad) {
                $cotizacion = $this->cotizacionService->obtenerPorIdOrden($presupuesto->orden_id);
                $matrizCotizacion = $this->matrizCotizacionService->obtenerIdDePrioridadYCondicion($request->prioridad, $cotizacion->condicion);

                $diferenciaCotizacion = $matrizCotizacion->precio_venta - $cotizacion->venta;
            }
            $totalDiferenciaMonto = $montoDiferenciaPresupuestoNew + $diferenciaCotizacion;
            if ($totalDiferenciaMonto > 0) {
                if ($this->causaService->noPasoValidacionEAPECausa($orden->causa_id, $totalDiferenciaMonto)) {
                    return response()->json([
                        'message' => 'ALERTA!
                     Su solicitud no puede concretarse por falta de saldo en la billetera. Por favor, agregue saldo y luego vuelva a intentarlo.',
                        'data' => null
                    ], 409);
                }
            }


            $presupuesto = $this->presupuestoService->update($data, $presupuesto->id);

            if ($request->prioridad) {
                //ACTUALIZACION DE ORDENS
                $cotizacion = $this->cotizacionService->obtenerPorIdOrden($presupuesto->orden_id);
                $matrizCotizacion = $this->matrizCotizacionService->obtenerIdDePrioridadYCondicion($request->prioridad, $cotizacion->condicion);
                $dataOrden = [
                    'prioridad' => $request->prioridad,
                    'procurador_id' => $request->procurador_id,
                    'matriz_id' => $matrizCotizacion->id,
                ];
                $orden = $this->ordenService->update($dataOrden, $presupuesto->orden_id);

                //ACTUALIZACION DE COTIZACION
                $dataCotizacion = [
                    'compra' => $matrizCotizacion->precio_compra,
                    'venta' => $matrizCotizacion->precio_venta,
                    'penalizacion' => $matrizCotizacion->penalizacion,
                    'prioridad' => $matrizCotizacion->numero_prioridad
                ];
                $cotizacion = $this->cotizacionService->update($dataCotizacion, $cotizacion->id);
            }
        } else {
            return response()->json([
                'message' => 'ALERTA!
                 Presupuesto entregado, no se puede modificar el presupuesto.',
                'data' => null
            ], 409);
        }
        $data = [
            'message' => MessageHttp::ACTUALIZADO_CORRECTAMENTE,
            'data' => $presupuesto
        ];
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Presupuesto $presupuesto)
    {
        $presupuesto = $this->presupuestoService->destroy($presupuesto);
        $data = [
            'message' => MessageHttp::ELIMINADO_CORRECTAMENTE,
            'data' => $presupuesto
        ];
        return response()->json($data);
    }
    public function entregarPresupuesto(Presupuesto $presupuesto)
    {
        $now = Carbon::now('America/La_Paz');
        $fechaHora = $now->toDateTimeString();
        $data = [
            'fecha_entrega' => $fechaHora
        ];
        $presupuesto = $this->presupuestoService->update($data, $presupuesto->id);

        //ACTUALIZA ETAPA DE LA ORDEN
        $dataOrden = [
            'etapa_orden' => EtapaOrden::DINERO_ENTREGADO,
        ];
        $orden = $this->ordenService->update($dataOrden, $presupuesto->orden_id);

        $data = [
            'message' => 'Dinero entregado correctamente',
            'data' => $presupuesto
        ];
        return response()->json($data);
    }
    public function entregarPresupuestosMasivo(Request $request)
    {
        $now = Carbon::now('America/La_Paz');
        $fechaHora = $now->toDateTimeString();

        $presupuestos = $request->validate([
            '*.id' => 'required|integer|exists:presupuestos,id',
            '*.monto' => 'required'
        ]);
        // Validación previa: ninguno debe tener fecha_entrega
        foreach ($presupuestos as $item) {
            $pres = Presupuesto::find($item['id']);
            if ($pres->fecha_entrega !== null) {
                return response()->json([
                    'message' => "La orden {$pres->orden_id} ya tiene presupuesto entregado.",
                    'data'    => null
                ], 409);
            }
        }
        //Suma de totales
        $total = collect($presupuestos)
            ->sum(function ($item) {
                return (float) ($item['monto'] ?? 0);
            });
        //Validacion de caja del contador
        $tablaConfig = TablaConfig::findOrFail(1);
        if ($total > $tablaConfig->caja_contador) {
            return response()->json([
                'message' => 'Usted no tiene suficiente saldo para realizar esta accion, Saldo actual = ' . $tablaConfig->caja_contador,
                'data' => null
            ], 409);
        }
        DB::beginTransaction();

        try {
            $ordenesEntregadas = [];
            foreach ($presupuestos as $item) {
                $presupuesto = Presupuesto::find($item['id']);
                // Entregar presupuesto
                $dataPresupuesto = [
                    'fecha_entrega' => $fechaHora
                ];
                $presupuesto = $this->presupuestoService->update($dataPresupuesto, $presupuesto->id);
                // Actualizar orden
                $dataOrden = [
                    'etapa_orden' => EtapaOrden::DINERO_ENTREGADO,
                ];
                $orden = $this->ordenService->update($dataOrden, $presupuesto->orden_id);
                $ordenesEntregadas[] = $presupuesto->orden_id;
            }
            //Registro de transaccion contador
            if ($total > 0) {
                $dataTrnContador = [
                    'monto' => $total,
                    'fecha_transaccion' => $fechaHora,
                    'tipo' => TipoTransaccion::DEBITO,
                    'transaccion' => Transaccion::EGRESO_POR_ENTREGA_PRESUPUESTO,
                    'glosa' => GlosaTransaccion::DEBITO_POR_ENTREGA_PRESUPUESTO . '[' . implode(',', $ordenesEntregadas) . ']',
                    'contador_id' => Auth::user()->id,
                    'usuario_id' => Auth::user()->id
                ];
                $transaccionContador = $this->transaccionesContadorService->registrarTransaccionContador($dataTrnContador);
            }

            DB::commit();
            return response()->json([
                'message' => 'Presupuestos entregados correctamente',
                'data' => null
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error registrar entrega: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error registrar entrega',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
