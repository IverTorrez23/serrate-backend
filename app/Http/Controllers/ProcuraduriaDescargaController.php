<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Constants\Estado;
use App\Enums\MessageHttp;
use Illuminate\Http\Request;
use App\Constants\EtapaOrden;
use App\Services\OrdenService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ProcuraduriaDescarga;
use App\Services\PresupuestoService;

use App\Services\ConfirmacionService;
use App\Services\ProcuraduriaDescargaService;
use App\Http\Resources\ProcuraduriaDescargaCollection;
use App\Http\Requests\StoreProcuraduriaDescargaRequest;
use App\Services\CausaService;

class ProcuraduriaDescargaController extends Controller
{
    protected $procuraduriaDescargaService;
    protected $presupuestoService;
    protected $ordenService;
    protected $confirmacionService;
    protected $causaService;

    public function __construct(
        ProcuraduriaDescargaService $procuraduriaDescargaService,
        PresupuestoService $presupuestoService,
        OrdenService $ordenService,
        ConfirmacionService $confirmacionService,
        CausaService $causaService
    ) {
        $this->procuraduriaDescargaService = $procuraduriaDescargaService;
        $this->presupuestoService = $presupuestoService;
        $this->ordenService = $ordenService;
        $this->confirmacionService = $confirmacionService;
        $this->causaService = $causaService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $descarga = $this->procuraduriaDescargaService->index();
        return new ProcuraduriaDescargaCollection($descarga);
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
    public function store(StoreProcuraduriaDescargaRequest $request)
    {
        $gastoDescarga = $request->gastos;
        $orden = $this->ordenService->obtenerUno($request->orden_id);
        if ($this->causaService->cuasaNoEstaActiva($orden->causa_id)) {
            return response()->json([
                'message' => 'No se puede realizar la descarga, porque la causa no está activa.',
                'data' => null
            ], 409);
        }
        $tieneRegistroDeDescarga = $this->procuraduriaDescargaService->tieneDescargaActiva($request->orden_id);
        if ($tieneRegistroDeDescarga) {
            return response()->json([
                'message' => 'Esta orden ya tiene una descarga',
                'data' => null
            ], 409);
        }
        $presupuesto = $this->presupuestoService->obtenerUnoPorOrdenId($request->orden_id);
        if ($gastoDescarga > $presupuesto->monto) {
            $montoProbable = $gastoDescarga - $presupuesto->monto;
            if ($this->causaService->noPasoValidacionEAPECausa($orden->causa_id, $montoProbable)) {
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
            $fechaHora = $now->format('Y-m-d H:i:00');

            //$presupuesto = $this->presupuestoService->obtenerUnoPorOrdenId($request->orden_id);
            $saldo = $presupuesto->monto - $gastoDescarga;

            $data = [
                'detalle_informacion' => $request->detalle_informacion,
                'detalle_documentacion' => $request->detalle_documentacion,
                'ultima_foja' => $request->ultima_foja,
                'gastos' => $gastoDescarga,
                'saldo' => $saldo,
                'detalle_gasto' => $request->detalle_gasto,
                'fecha_descarga' => $fechaHora,
                'compra_judicial' => $gastoDescarga,
                'orden_id' => $request->orden_id,
            ];

            $descarga = $this->procuraduriaDescargaService->store($data);

            //ACTUALIZA LA ETAPA DE LA ORDEN
            $dataOrden = [
                'etapa_orden' => EtapaOrden::DESCARGADA
            ];
            $orden = $this->ordenService->update($dataOrden, $request->orden_id);
            //INSERTA LA CONFIRMACION DEL SISTEMA
            $confirmacionSistema = $descarga->fecha_descarga <= $orden->fecha_fin ? 1 : 0;

            $dataConfirmacion = [
                'confir_sistema' => $confirmacionSistema,
                'descarga_id' => $descarga->id
            ];
            $confirmacion = $this->confirmacionService->store($dataConfirmacion);

            DB::commit();
            return response()->json([
                'message' => MessageHttp::CREADO_CORRECTAMENTE,
                'data' => $descarga
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
    public function show(ProcuraduriaDescarga $procuraduriaDescarga)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProcuraduriaDescarga $procuraduriaDescarga)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProcuraduriaDescarga $procuraduriaDescarga)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProcuraduriaDescarga $procuraduriaDescarga)
    {
        //
    }
    public function ultinaFojaCausa($causaId)
    {
        $ultimaFojaDescarga = $this->procuraduriaDescargaService->ultimaFojaDeCausa($causaId);
        return response()->json([
            'message' => 'Datos obtenidos',
            'data' => $ultimaFojaDescarga
        ], 200);
    }
}
