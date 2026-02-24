<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Orden;
use App\Constants\Estado;
use App\Enums\MessageHttp;
use Illuminate\Http\Request;
use App\Constants\EtapaOrden;
use App\Constants\TipoUsuario;
use App\Services\CausaService;
use App\Services\OrdenService;
use Illuminate\Support\Facades\DB;
use App\Services\CotizacionService;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Auth;
use App\Http\Resources\OrdenCollection;
use App\Http\Requests\StoreOrdenRequest;
use App\Http\Requests\UpdateOrdenRequest;
use App\Services\MatrizCotizacionService;
use App\Services\ListaOrdenGiradasService;
use App\Services\ListaOrdenAceptadasService;
use App\Services\ContadorOrdenGiradasService;
use App\Services\ListaOrdenDescargadasService;
use App\Services\ContadorOrdenAceptadasService;
use App\Services\ContadorOrdenDescargadaService;
use App\Services\ListaOrdenListaRealizarService;
use App\Services\ListaOrdenPresupuestadaService;
use App\Services\ListaOrdenVencidasLevesService;
use App\Services\ListaOrdenVencidasGravesService;
use App\Services\ListaOrdenDineroEntregadoService;
use App\Services\ContadorOrdenPresupuestadaService;
use App\Services\ContadorOrdenVencidasLevesService;
use App\Services\ListaOrdenCuentaConciliadaService;
use App\Services\ListaOrdenPronuncioAbogadoService;
use App\Services\ContadorOrdenListasRealizarService;
use App\Services\ContadorOrdenVencidasGravesService;
use App\Services\ListaOrdenPrePresupuestadasService;
use App\Services\ContadorOrdenDineroEntregadoService;
use App\Services\ContadorOrdenPrePresupuestadaService;
use App\Services\ContadorOrdenPronuncioAbogadoService;
use App\Services\ContadorOrdenCuentasConciliadaService;
use App\Services\ParametroVigenciaService;
use App\Services\SeguimientoLider\ContadorOrdenesDeLiderService;
use App\Services\SeguimientoLider\ListadoOrdenesSeguimientoDeLiderService;

class OrdenController extends Controller
{
    protected $matrizCotizacionService;
    protected $cotizacionService;
    protected $ordenService;
    protected $causaService;
    protected $contadorOrdenGiradasService;
    protected $contadorOrdenPrePresupuestadasService;
    protected $contadorOrdenPresupuestadaService;
    protected $contadorOrdenAceptadaService;
    protected $contadorOrdenDineroEntregadoService;
    protected $contadorOrdenListasRealizarService;
    protected $contadorOrdenDescargadaService;
    protected $contadorOrdenPronuncioAbogadoService;
    protected $contadorOrdenCuentasConciliadaService;
    protected $contadorOrdenVencidasLevesService;
    protected $contadorOrdenVencidasGravesService;
    protected $listaOrdenGiradasService;
    protected $listaOrdenPrePresupuestadasService;
    protected $listaOrdenPresupuestadaService;
    protected $listaOrdenAceptadasService;
    protected $listaOrdenDineroEntregadoService;
    protected $listaOrdenListaRealizarService;
    protected $listaOrdenDescargadasService;
    protected $listaOrdenPronuncioAbogadoService;
    protected $listaOrdenCuentaConciliadaService;
    protected $listaOrdenVencidasLevesService;
    protected $listaOrdenVencidasGravesService;
    protected $contadorOrdenesDeLiderService;
    protected $listadoOrdenesSeguimientoDeLiderService;
    protected $parametroVigenciaService;

    public function __construct(
        MatrizCotizacionService $matrizCotizacionService,
        CotizacionService $cotizacionService,
        OrdenService $ordenService,
        CausaService $causaService,
        ContadorOrdenGiradasService $contadorOrdenGiradasService,
        ContadorOrdenPrePresupuestadaService $contadorOrdenPrePresupuestadasService,
        ContadorOrdenPresupuestadaService $contadorOrdenPresupuestadaService,
        ContadorOrdenAceptadasService $contadorOrdenAceptadaService,
        ContadorOrdenDineroEntregadoService $contadorOrdenDineroEntregadoService,
        ContadorOrdenListasRealizarService $contadorOrdenListasRealizarService,
        ContadorOrdenDescargadaService $contadorOrdenDescargadaService,
        ContadorOrdenPronuncioAbogadoService $contadorOrdenPronuncioAbogadoService,
        ContadorOrdenCuentasConciliadaService $contadorOrdenCuentasConciliadaService,
        ContadorOrdenVencidasLevesService $contadorOrdenVencidasLevesService,
        ContadorOrdenVencidasGravesService $contadorOrdenVencidasGravesService,
        ListaOrdenGiradasService $listaOrdenGiradasService,
        ListaOrdenPrePresupuestadasService $listaOrdenPrePresupuestadasService,
        ListaOrdenPresupuestadaService $listaOrdenPresupuestadaService,
        ListaOrdenAceptadasService $listaOrdenAceptadasService,
        ListaOrdenDineroEntregadoService $listaOrdenDineroEntregadoService,
        ListaOrdenListaRealizarService $listaOrdenListaRealizarService,
        ListaOrdenDescargadasService $listaOrdenDescargadasService,
        ListaOrdenPronuncioAbogadoService $listaOrdenPronuncioAbogadoService,
        ListaOrdenCuentaConciliadaService $listaOrdenCuentaConciliadaService,
        ListaOrdenVencidasLevesService $listaOrdenVencidasLevesService,
        ListaOrdenVencidasGravesService $listaOrdenVencidasGravesService,
        //Orden de lider
        ContadorOrdenesDeLiderService $contadorOrdenesDeLiderService,
        ListadoOrdenesSeguimientoDeLiderService $listadoOrdenesSeguimientoDeLiderService,
        ParametroVigenciaService $parametroVigenciaService
    ) {
        $this->matrizCotizacionService = $matrizCotizacionService;
        $this->cotizacionService = $cotizacionService;
        $this->ordenService = $ordenService;
        $this->causaService = $causaService;
        //Contadores de ordenes
        $this->contadorOrdenGiradasService = $contadorOrdenGiradasService;
        $this->contadorOrdenPrePresupuestadasService = $contadorOrdenPrePresupuestadasService;
        $this->contadorOrdenPresupuestadaService = $contadorOrdenPresupuestadaService;
        $this->contadorOrdenAceptadaService = $contadorOrdenAceptadaService;
        $this->contadorOrdenDineroEntregadoService = $contadorOrdenDineroEntregadoService;
        $this->contadorOrdenListasRealizarService = $contadorOrdenListasRealizarService;
        $this->contadorOrdenDescargadaService = $contadorOrdenDescargadaService;
        $this->contadorOrdenPronuncioAbogadoService = $contadorOrdenPronuncioAbogadoService;
        $this->contadorOrdenCuentasConciliadaService = $contadorOrdenCuentasConciliadaService;
        $this->contadorOrdenVencidasLevesService = $contadorOrdenVencidasLevesService;
        $this->contadorOrdenVencidasGravesService = $contadorOrdenVencidasGravesService;
        //Lista para seguimiento
        $this->listaOrdenGiradasService = $listaOrdenGiradasService;
        $this->listaOrdenPrePresupuestadasService = $listaOrdenPrePresupuestadasService;
        $this->listaOrdenPresupuestadaService = $listaOrdenPresupuestadaService;
        $this->listaOrdenAceptadasService = $listaOrdenAceptadasService;
        $this->listaOrdenDineroEntregadoService = $listaOrdenDineroEntregadoService;
        $this->listaOrdenListaRealizarService = $listaOrdenListaRealizarService;
        $this->listaOrdenDescargadasService = $listaOrdenDescargadasService;
        $this->listaOrdenPronuncioAbogadoService = $listaOrdenPronuncioAbogadoService;
        $this->listaOrdenCuentaConciliadaService = $listaOrdenCuentaConciliadaService;
        $this->listaOrdenVencidasLevesService = $listaOrdenVencidasLevesService;
        $this->listaOrdenVencidasGravesService = $listaOrdenVencidasGravesService;
        //ordenes de lider
        $this->contadorOrdenesDeLiderService = $contadorOrdenesDeLiderService;
        $this->listadoOrdenesSeguimientoDeLiderService = $listadoOrdenesSeguimientoDeLiderService;
        $this->parametroVigenciaService = $parametroVigenciaService;
    }

    public function index(Request $request)
    {
        $ordenes = $this->ordenService->index($request);
        return new OrdenCollection($ordenes);
    }


    public function listarPorCausa(Request $request, $idCausa)
    {
        $tipoUsuario = Auth::user()->tipo;
        if ($tipoUsuario === TipoUsuario::ABOGADO_INDEPENDIENTE || $tipoUsuario === TipoUsuario::ABOGADO_LIDER || $tipoUsuario === TipoUsuario::ABOGADO_DEPENDIENTE) {
            if (!$this->causaService->abogadoTienePermisoCausa($idCausa)) {
                return response()->json(['message' => 'No esta autorizado para ver estos datos'], 403);
            }
        }
        $ordenCausa = $this->ordenService->listarPorCausa($request, $idCausa);
        return new OrdenCollection($ordenCausa);
    }
    public function listarPorCausaDeProcurador(Request $request, $idCausa, $procuradorId)
    {
        $tipoUsuario = Auth::user()->tipo;
        if ($tipoUsuario === TipoUsuario::PROCURADOR) {
            $ordenCausa = $this->ordenService->getOrdenesProcurador($request, $idCausa, $procuradorId);
        } else {
            $ordenCausa = $this->ordenService->getOrdenesTodosProcurador($request, $idCausa);
        }

        return new OrdenCollection($ordenCausa);
    }

    public function store(StoreOrdenRequest $request)
    {
        //hace la validacion para ver si es un abogado permitido para girar orden
        if (!$this->causaService->abogadoTienePermisoCausa($request->causa_id)) {
            return response()->json(['message' => 'No esta autorizado para realizar esta acción'], 403);
        }
        //hace la validacion para ver si la causa esta activa
        if ($this->causaService->cuasaNoEstaActiva($request->causa_id)) {
            return response()->json([
                'message' => 'No se puede girar orden, porque la causa no está activa.',
                'data' => null
            ], 409);
        }
        //Validacion si hay ppaquete vigente
        $causa = $this->causaService->obtenerUnaCausa($request->causa_id);
        $idUsuario = $causa->usuario_id;

        if (!$this->parametroVigenciaService->hayPaqueteVigente($idUsuario)) {
            $causas = $this->causaService->actualizarEstadoPorUsuario($idUsuario);
            return response()->json([
                'message' => 'No se puede girar orden, porque no hay paquetes vigentes.',
                'data' => null
            ], 409);
        }
        //Otiene la cotizacion
        $response = $this->obtenetMatrizCotizacion($request->fecha_inicio, $request->fecha_fin, $request->prioridad);
        $matrizCotizacion = $response['matrizCotizacion'];

        if ($this->causaService->noPasoValidacionEAPECausa($request->causa_id, $matrizCotizacion->precio_venta)) {
            return response()->json([
                'message' => 'ALERTA!
                 Su solicitud no puede concretarse por falta de saldo en la billetera. Por favor, agregue saldo y luego vuelva a intentarlo.',
                'data' => null
            ], 409);
        }

        DB::beginTransaction();
        try {

            $difference = $response['difference'];
            $now = Carbon::now('America/La_Paz');
            $fechaHora = $now->toDateTimeString();
            $tipo = Auth::user()->tipo;
            $data = [
                'entrega_informacion' => $request->entrega_informacion,
                'entrega_documentacion' => $request->entrega_documentacion,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'fecha_giro' => $fechaHora,
                'plazo_hora' => $difference,
                'fecha_recepcion' => null,
                'etapa_orden' => EtapaOrden::GIRADA,
                'prioridad' => $request->prioridad,
                'girada_por' => $tipo,
                'fecha_ini_bandera' => $request->fecha_inicio,
                'notificado' => 0,
                'lugar_ejecucion' => $request->lugar_ejecucion,
                'sugerencia_presupuesto' => null, //$request->sugerencia_presupuesto, // vacio
                'tiene_propina' => $request->tiene_propina,
                'propina' => $request->propina,
                'causa_id' => $request->causa_id,
                'procurador_id' => $request->procurador_id,
                'matriz_id' => $matrizCotizacion->id,
                'usuario_id' => Auth::user()->id
            ];

            $orden = $this->ordenService->store($data);

            //Registro de cotizacion
            $dataCotizacion = [
                'compra' => $matrizCotizacion->precio_compra,
                'venta' => $matrizCotizacion->precio_venta,
                'penalizacion' => $matrizCotizacion->penalizacion,
                'prioridad' => $request->prioridad,
                'condicion' => $matrizCotizacion->condicion,
                'orden_id' => $orden->id, // ID de la orden obtenida
            ];
            // Llamar al método store del servicio
            $cotizacion = $this->cotizacionService->store($dataCotizacion);
            DB::commit();
            return response()->json([
                'message' => MessageHttp::CREADO_CORRECTAMENTE,
                'data' => $orden
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al crear la orden: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error al crear la orden',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Orden $orden)
    {
        $tipoUsuario = Auth::user()->tipo;
        if ($tipoUsuario === TipoUsuario::ABOGADO_INDEPENDIENTE || $tipoUsuario === TipoUsuario::ABOGADO_LIDER || $tipoUsuario === TipoUsuario::ABOGADO_DEPENDIENTE) {
            if (!$this->causaService->abogadoTienePermisoCausa($orden->causa_id)) {
                return response()->json(['message' => 'No esta autorizado para ver estos datos'], 403);
            }
        }
        $data = $this->ordenService->listarOrden($orden);

        return response()->json($data);
    }


    public function update(UpdateOrdenRequest $request, Orden $orden)
    {

        //hace la validacion para ver si es un abogado permitido para girar orden
        if (!$this->causaService->abogadoTienePermisoCausa($request->causa_id)) {
            return response()->json(['message' => 'No esta autorizado para realizar esta acción'], 403);
        }
        if ($this->causaService->cuasaNoEstaActiva($request->causa_id)) {
            return response()->json([
                'message' => 'No se puede girar orden, porque la causa no está activa.',
                'data' => null
            ], 409);
        }
        $presupuesto = $orden->presupuesto;
        if ($presupuesto) {
            return response()->json([
                'message' => 'No se puede actualizar la orden, porque la orden ya tiene presupuesto.',
                'data' => null
            ], 409);
        }

        //Obtiene la cotizacion con los datos de la orden
        $response = $this->obtenetMatrizCotizacion($request->fecha_inicio, $request->fecha_fin, $request->prioridad);
        $matrizCotizacion = $response['matrizCotizacion'];
        //Cotizacion anteriormente guardada
        $cotizacion = $this->cotizacionService->obtenerPorIdOrden($orden->id);
        $diferenciaCotizacion = $matrizCotizacion->precio_venta - $cotizacion->venta;
        if ($diferenciaCotizacion > 0) {
            if ($this->causaService->noPasoValidacionEAPECausa($request->causa_id, $diferenciaCotizacion)) {
                return response()->json([
                    'message' => 'ALERTA!
                 Su solicitud no puede concretarse por falta de saldo en la billetera. Por favor, agregue saldo y luego vuelva a intentarlo.',
                    'data' => null
                ], 409);
            }
        }

        DB::beginTransaction();
        try {
            $response = $this->obtenetMatrizCotizacion($request->fecha_inicio, $request->fecha_fin, $request->prioridad);
            $matrizCotizacion = $response['matrizCotizacion'];
            $difference = $response['difference'];
            $data = $request->only([
                'entrega_informacion',
                'entrega_documentacion',
                'fecha_inicio',
                'fecha_fin',
                'prioridad',
                'lugar_ejecucion',
                'tiene_propina',
                'propina',
                'procurador_id'
            ]);
            $data['matriz_id'] = $matrizCotizacion->id;
            $data['plazo_hora'] = $difference;

            $orden = $this->ordenService->update($data, $orden->id);
            //Actualizacion de cotizacion
            $cotizacion = $this->cotizacionService->obtenerPorIdOrden($orden->id);
            $dataCotizacion = [
                'compra' => $matrizCotizacion->precio_compra,
                'venta' => $matrizCotizacion->precio_venta,
                'penalizacion' => $matrizCotizacion->penalizacion,
                'prioridad' => $request->prioridad,
                'condicion' => $matrizCotizacion->condicion
            ];

            $cotizacion = $this->cotizacionService->update($dataCotizacion, $cotizacion->id);

            DB::commit();
            $data = [
                'message' => MessageHttp::ACTUALIZADO_CORRECTAMENTE,
                'data' => $orden
            ];
            return response()->json($data);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error actualizando la orden: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error actualizando la orden',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Orden $orden)
    {
        // Obtener el presupuesto relacionado con la orden
        $presupuesto = $orden->presupuesto;
        // Verificar si el presupuesto existe y si el campo fecha_entrega está vacío
        if ($presupuesto && !empty($presupuesto->fecha_entrega)) {
            // Si fecha_entrega no está vacío, no permitir la eliminación
            return response()->json([
                'message' => 'No se puede eliminar la orden porque el presupuesto ya tiene una fecha de entrega.',
                'data' => null
            ], 409);
        }
        $orden = $this->ordenService->destroy($orden->id);
        $data = [
            'message' => MessageHttp::ELIMINADO_CORRECTAMENTE,
            'data' => $orden
        ];
        return response()->json($data);
    }

    public function aceptarOrden(Orden $orden)
    {
        if ($orden->fecha_recepcion) {
            return response()->json([
                'message' => 'Esta orden ya fue aceptada',
                'data' => null
            ], 409);
        }
        $now = Carbon::now('America/La_Paz');
        $fechaHora = $now->toDateTimeString();
        $data = [
            'fecha_recepcion' => $fechaHora,
            'etapa_orden' => EtapaOrden::ACEPTADA
        ];
        $orden = $this->ordenService->update($data, $orden->id);

        $data = [
            'message' => 'Orden aceptada correctamente',
            'data' => $orden
        ];
        return response()->json($data);
    }

    public function obtenetMatrizCotizacion($fechaInicio, $fechaFin, $prioridad)
    {
        $carbonFecha1 = Carbon::parse($fechaInicio);
        $carbonFecha2 = Carbon::parse($fechaFin);
        $difference = $carbonFecha1->diffInHours($carbonFecha2);
        $condicion = 0;
        if ($difference > 96) {
            $condicion = 1;
        }
        if ($difference > 24 && $difference <= 96) {
            $condicion = 2;
        }
        if ($difference > 8 && $difference <= 24) {
            $condicion = 3;
        }
        if ($difference > 3 && $difference <= 8) {
            $condicion = 4;
        }
        if ($difference > 1 && $difference <= 3) {
            $condicion = 5;
        }
        if ($difference <= 1) {
            $condicion = 6;
        }
        $matrizCotizacion = $this->matrizCotizacionService->obtenerIdDePrioridadYCondicion($prioridad, $condicion);
        return [
            'matrizCotizacion' => $matrizCotizacion,
            'difference' => $difference
        ];
    }
    public function sugerirPresupuesto(UpdateOrdenRequest $request, Orden $orden)
    {
        if ($orden->etapa_orden === EtapaOrden::GIRADA || $orden->etapa_orden === EtapaOrden::PREPRESUPUESTADA) {
            $data['sugerencia_presupuesto'] = $request->sugerencia_presupuesto;
            $data['etapa_orden'] = EtapaOrden::PREPRESUPUESTADA;
            $orden = $this->ordenService->update($data, $orden->id);
        } else {
            return 'No se puede sugerir presupuesto, porque la se giro el presupuesto';
        }

        return response()->json([
            'message' => MessageHttp::ACTUALIZADO_CORRECTAMENTE,
            'data' => $orden
        ], 200);
    }
    public function ordenesParaEntregarPresupuesto($procuradorId)
    {
        $data = $this->ordenService->listarOrdenParaEntregarPresupuestoDeProcurador($procuradorId);

        return response()->json($data);
    }
    public function ordenesParaDevolverPresupuesto($procuradorId)
    {
        $data = $this->ordenService->listarOrdenParaDevolverPresupuestoDeProcurador($procuradorId);

        return response()->json($data);
    }
    public function ordenesParaColocarCostoJudicialVenta()
    {
        $data = $this->ordenService->listarOrdenParaColocarCostoJudicialVenta();
        return response()->json($data);
    }
    public function cantidadOrdenesEnEtapas()
    {
        $cantidadGiradas = $this->contadorOrdenGiradasService->devuelveCantidadOrdenesGiradas();
        $cantidadPrePresupuestadas = $this->contadorOrdenPrePresupuestadasService->devuelveCantidadOrdenesPrePresupuestadas();
        $cantidadPresupuestadas = $this->contadorOrdenPresupuestadaService->devuelveCantidadOrdenesPresupuestadas();
        $cantidadAceptadas = $this->contadorOrdenAceptadaService->devuelveCantidadOrdenesAceptadas();
        $cantidadDineroEntregado = $this->contadorOrdenDineroEntregadoService->devuelveCantidadOrdenesDineroEntregado();
        $cantidadListasRealizar = $this->contadorOrdenListasRealizarService->devuelveCantidadOrdenesListasRealizar();
        $cantidadDescargada = $this->contadorOrdenDescargadaService->devuelveCantidadOrdenesDescargadas();
        $cantidadPronuncioAbogado = $this->contadorOrdenPronuncioAbogadoService->devuelveCantidadOrdenesPronuncioAbogado();
        $cantidadCuentasConciliadas = $this->contadorOrdenCuentasConciliadaService->devuelveCantidadOrdenesCuentasConciliadas();
        $cantidadVencidasLeves = $this->contadorOrdenVencidasLevesService->devuelveCantidadOrdenesVencidasLeves();
        $cantidadVencidasGraves = $this->contadorOrdenVencidasGravesService->devuelveCantidadOrdenesVencidasGraves();
        $data = [
            'cantidad_giradas' => $cantidadGiradas,
            'cantidad_pre_presupuestadas' => $cantidadPrePresupuestadas,
            'cantidad_presupuestadas' => $cantidadPresupuestadas,
            'cantidad_aceptadas' => $cantidadAceptadas,
            'cantidad_dinero_entregado' => $cantidadDineroEntregado,
            'cantidad_lista_realizar' => $cantidadListasRealizar,
            'cantidad_descargadas' => $cantidadDescargada,
            'cantidad_pronuncio_abogado' => $cantidadPronuncioAbogado,
            'cantidad_cuentas_conciliadas' => $cantidadCuentasConciliadas,
            'cantidad_vencidas_leves' => $cantidadVencidasLeves,
            'cantidad_vencidas_graves' => $cantidadVencidasGraves
        ];
        return response()->json([
            'message' => MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data' => $data
        ], 200);
    }
    public function cantidadOrdenesEnEtapasDeLider()
    {
        $cantidadGiradas = $this->contadorOrdenesDeLiderService->contarOrdenesGiradaDeLider();
        $cantidadPrePresupuestadas = $this->contadorOrdenesDeLiderService->contarOrdenesPrePresupuestadasDeLider();
        $cantidadPresupuestadas = $this->contadorOrdenesDeLiderService->contarOrdenesPresupuestadasDeLider();
        $cantidadAceptadas = $this->contadorOrdenesDeLiderService->contarOrdenesAceptadasDeLider();
        $cantidadDineroEntregado = $this->contadorOrdenesDeLiderService->contarOrdenesDineroEntregadoDeLider();
        $cantidadListasRealizar = $this->contadorOrdenesDeLiderService->contarOrdenesListasRealizarDeLider();
        $cantidadDescargada = $this->contadorOrdenesDeLiderService->contarOrdenesDescargadaDeLider();
        $cantidadPronuncioAbogado = $this->contadorOrdenesDeLiderService->contarOrdenesPronuncioAbogadoDeLider();
        $cantidadCuentasConciliadas = $this->contadorOrdenesDeLiderService->contarOrdenesCuentaConciliadaDeLider();
        $cantidadVencidasLeves = $this->contadorOrdenesDeLiderService->contarOrdenesVencidasLevesDeLider();
        $cantidadVencidasGraves = $this->contadorOrdenesDeLiderService->contarOrdenesVencidasGravesDeLider();
        $data = [
            'cantidad_giradas' => $cantidadGiradas,
            'cantidad_pre_presupuestadas' => $cantidadPrePresupuestadas,
            'cantidad_presupuestadas' => $cantidadPresupuestadas,
            'cantidad_aceptadas' => $cantidadAceptadas,
            'cantidad_dinero_entregado' => $cantidadDineroEntregado,
            'cantidad_lista_realizar' => $cantidadListasRealizar,
            'cantidad_descargadas' => $cantidadDescargada,
            'cantidad_pronuncio_abogado' => $cantidadPronuncioAbogado,
            'cantidad_cuentas_conciliadas' => $cantidadCuentasConciliadas,
            'cantidad_vencidas_leves' => $cantidadVencidasLeves,
            'cantidad_vencidas_graves' => $cantidadVencidasGraves
        ];
        return response()->json([
            'message' => MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data' => $data
        ], 200);
    }
    public function listadoOrdenGiradas(Request $request, $idCausa)
    {
        try {
            $ordenes = $this->listaOrdenGiradasService->devuelveListaOrdenGiradas($request, $idCausa);
            return new OrdenCollection($ordenes);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las ordenes.',
                'data' => null
            ], 500);
        }
    }
    public function listadoOrdenPrePresupuestadas(Request $request, $idCausa)
    {
        try {
            $ordenes = $this->listaOrdenPrePresupuestadasService->devuelveListaOrdenPrePresupuestadas($request, $idCausa);
            return new OrdenCollection($ordenes);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las ordenes.',
                'data' => null
            ], 500);
        }
    }
    public function listadoOrdenPresupuestadas(Request $request, $idCausa)
    {
        try {
            $ordenes = $this->listaOrdenPresupuestadaService->devuelveListaOrdenPresupuestadas($request, $idCausa);
            return new OrdenCollection($ordenes);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las ordenes.',
                'data' => null
            ], 500);
        }
    }
    public function listadoOrdenAceptadas(Request $request, $idCausa)
    {
        try {
            $ordenes = $this->listaOrdenAceptadasService->devuelveListaOrdenAceptadas($request, $idCausa);
            return new OrdenCollection($ordenes);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las ordenes.',
                'data' => null
            ], 500);
        }
    }
    public function listadoOrdenDineroEntregado(Request $request, $idCausa)
    {
        try {
            $ordenes = $this->listaOrdenDineroEntregadoService->devuelveListaOrdenDineroEntregado($request, $idCausa);
            return new OrdenCollection($ordenes);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las ordenes.',
                'data' => null
            ], 500);
        }
    }
    public function listadoOrdenListaRealizar(Request $request, $idCausa)
    {
        try {
            $ordenes = $this->listaOrdenListaRealizarService->devuelveListaOrdenListaRealizar($request, $idCausa);
            return new OrdenCollection($ordenes);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las ordenes.',
                'data' => null
            ], 500);
        }
    }

    public function listadoOrdenDescargadas(Request $request, $idCausa)
    {
        try {
            $ordenes = $this->listaOrdenDescargadasService->devuelveListaOrdenDescargadas($request, $idCausa);
            return new OrdenCollection($ordenes);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las ordenes.',
                'data' => null
            ], 500);
        }
    }
    public function listadoOrdenPronuncioAbogado(Request $request, $idCausa)
    {
        try {
            $ordenes = $this->listaOrdenPronuncioAbogadoService->devuelveListaOrdenPronuncioAbogado($request, $idCausa);
            return new OrdenCollection($ordenes);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las ordenes.',
                'data' => null
            ], 500);
        }
    }
    public function listadoOrdenCuentaConciliadas(Request $request, $idCausa)
    {
        try {
            $ordenes = $this->listaOrdenCuentaConciliadaService->devuelveListaOrdenCuentaConciliadas($request, $idCausa);
            return new OrdenCollection($ordenes);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las ordenes.',
                'data' => null
            ], 500);
        }
    }

    public function listadoOrdenVencidasLeves(Request $request, $idCausa)
    {
        try {
            $ordenes = $this->listaOrdenVencidasLevesService->devuelveListaOrdenVencidasLeves($request, $idCausa);
            return new OrdenCollection($ordenes);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las ordenes.',
                'data' => null
            ], 500);
        }
    }

    public function listadoOrdenVencidasGraves(Request $request, $idCausa)
    {
        try {
            $ordenes = $this->listaOrdenVencidasGravesService->devuelveListaOrdenVencidasGraves($request, $idCausa);
            return new OrdenCollection($ordenes);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las ordenes.',
                'data' => null
            ], 500);
        }
    }
    //Listado de ordenes de Lider
    public function listadoOrdenGiradasDeLider(Request $request, $idCausa)
    {
        try {
            $ordenes = $this->listadoOrdenesSeguimientoDeLiderService->getOrdenesDeCausaGiradasDeLider($request, $idCausa);
            return new OrdenCollection($ordenes);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las ordenes.',
                'data' => null
            ], 500);
        }
    }
    public function listadoOrdenPrePresupuestadasDeLider(Request $request, $idCausa)
    {
        try {
            $ordenes = $this->listadoOrdenesSeguimientoDeLiderService->getOrdenesDeCausaPrePresupuestadasDeLider($request, $idCausa);
            return new OrdenCollection($ordenes);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las ordenes.',
                'data' => null
            ], 500);
        }
    }
    public function listadoOrdenPresupuestadasDeLider(Request $request, $idCausa)
    {
        try {
            $ordenes = $this->listadoOrdenesSeguimientoDeLiderService->getOrdenesDeCausaPresupuestadasDeLider($request, $idCausa);
            return new OrdenCollection($ordenes);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las ordenes.',
                'data' => null
            ], 500);
        }
    }
    public function listadoOrdenAceptadasDeLider(Request $request, $idCausa)
    {
        try {
            $ordenes = $this->listadoOrdenesSeguimientoDeLiderService->getOrdenesDeCausaAceptadasDeLider($request, $idCausa);
            return new OrdenCollection($ordenes);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las ordenes.',
                'data' => null
            ], 500);
        }
    }
    public function listadoOrdenDineroEntregadoDeLider(Request $request, $idCausa)
    {
        try {
            $ordenes = $this->listadoOrdenesSeguimientoDeLiderService->getOrdenesDeCausaDineroEntregadoDeLider($request, $idCausa);
            return new OrdenCollection($ordenes);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las ordenes.',
                'data' => null
            ], 500);
        }
    }
    public function listadoOrdenListaRealizarDeLider(Request $request, $idCausa)
    {
        try {
            $ordenes = $this->listadoOrdenesSeguimientoDeLiderService->getOrdenesDeCausaListaRealizarDeLider($request, $idCausa);
            return new OrdenCollection($ordenes);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las ordenes.',
                'data' => null
            ], 500);
        }
    }
    public function listadoOrdenDescargadasDeLider(Request $request, $idCausa)
    {
        try {
            $ordenes = $this->listadoOrdenesSeguimientoDeLiderService->getOrdenesDeCausaDescargadasDeLider($request, $idCausa);
            return new OrdenCollection($ordenes);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las ordenes.',
                'data' => null
            ], 500);
        }
    }
    public function listadoOrdenPronuncioAbogadoDeLider(Request $request, $idCausa)
    {
        try {
            $ordenes = $this->listadoOrdenesSeguimientoDeLiderService->getOrdenesDeCausaPronuncioAbogadoGeneral($request, $idCausa);
            return new OrdenCollection($ordenes);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las ordenes.',
                'data' => null
            ], 500);
        }
    }
    public function listadoOrdenCuentaConciliadasDeLider(Request $request, $idCausa)
    {
        try {
            $ordenes = $this->listadoOrdenesSeguimientoDeLiderService->getOrdenesDeCausaCuentaConciliadaDeLider($request, $idCausa);
            return new OrdenCollection($ordenes);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las ordenes.',
                'data' => null
            ], 500);
        }
    }
    public function listadoOrdenVencidasLevesDeLider(Request $request, $idCausa)
    {
        try {
            $ordenes = $this->listadoOrdenesSeguimientoDeLiderService->getOrdenesDeCausaVencidasLevesDeLider($request, $idCausa);
            return new OrdenCollection($ordenes);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las ordenes.',
                'data' => null
            ], 500);
        }
    }
    public function listadoOrdenVencidasGravesDeLider(Request $request, $idCausa)
    {
        try {
            $ordenes = $this->listadoOrdenesSeguimientoDeLiderService->getOrdenesDeCausaVencidasGravesDeLider($request, $idCausa);
            return new OrdenCollection($ordenes);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las ordenes.',
                'data' => null
            ], 500);
        }
    }
    public function ordenesListaOrdenCerradasParaPagoProcurador($procuradorId, Request $request)
    {
        $fechaInicioConsulta = $request->query('fecha_inicio_consulta');
        $fechaFinConsulta = $request->query('fecha_fin_consulta');
        $data = $this->ordenService->obtenerListaOrdenCerradasParaPagoProcurador($procuradorId, $fechaInicioConsulta, $fechaFinConsulta);
        return response()->json($data);
    }
    public function listarOrdenPorPisos()
    {

        $tipoUsuario = Auth::user()->tipo;
        if ($tipoUsuario == TipoUsuario::PROCURADOR) {
            $userId = Auth::user()->id;
            $ordenes = $this->ordenService->listarOrdenesActivasPorPisoProcurador($userId);
        } else {
            $ordenes = $this->ordenService->listarOrdenesActivasPorPiso();
        }

        return response()->json($ordenes);
    }
    public function listadoOrdenPorUrgencias()
    {
        $tipoUsuario = Auth::user()->tipo;
        if ($tipoUsuario == TipoUsuario::PROCURADOR) {
            $userId = Auth::user()->id;
            $ordenes = $this->ordenService->obtenerOrdenesPorUrgenciasProcurador($userId);
        } else {
            $ordenes = $this->ordenService->obtenerOrdenesPorUrgencias();
        }

        return response()->json($ordenes);
    }
    public function listadoOrdenEjecutar()
    {
        $tipoUsuario = Auth::user()->tipo;
        if ($tipoUsuario == TipoUsuario::PROCURADOR) {
            $userId = Auth::user()->id;
            $ordenes = $this->ordenService->obtenerOrdenesEjecutarProcurador($userId);
        } else {
            $ordenes = $this->ordenService->obtenerOrdenesEjecutar();
        }

        return response()->json($ordenes);
    }
    public function sumatoriaGastoPorCausaYFecha($causaId, $fechaCierre)
    {
        $sumatoria = $this->ordenService->sumatoriaGastoPorCausaYFecha($causaId, $fechaCierre);
        // return response()->json($sumatoria);
        return response()->json([
            'message' => MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data' => $sumatoria
        ], 200);
    }
    public function listadoDetalleFinancieroCausa($causaId)
    {
        $ordenes = $this->ordenService->obtenerOrdenesDetalleFinancieroDeCausa($causaId);
        return response()->json($ordenes);
    }
}
