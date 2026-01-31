<?php

namespace App\Http\Controllers;

use App\Constants\GlosaTransaccion;
use Exception;
use Carbon\Carbon;
use App\Enums\MessageHttp;
use Illuminate\Http\Request;
use App\Models\CompraPaquete;
use App\Services\PaqueteService;
use App\Services\CausaService;

use App\Constants\TipoTransaccion;
use App\Services\BilleteraService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\CompraPaqueteService;
use App\Http\Resources\CompraPaqueteCollection;
use App\Http\Requests\StoreCompraPaqueteRequest;
use App\Services\BilleteraTransaccionService;
use App\Services\ParametroVigenciaService;

class CompraPaqueteController extends Controller
{
    protected $compraPaqueteService;
    protected $paqueteService;
    protected $billeteraService;
    protected $billeteraTransaccionService;
    protected $parametroVigenciaService;
    protected $causaService;

    public function __construct(
        CompraPaqueteService $compraPaqueteService,
        PaqueteService $paqueteService,
        BilleteraService $billeteraService,
        BilleteraTransaccionService $billeteraTransaccionService,
        ParametroVigenciaService $parametroVigenciaService,
        CausaService $causaService
    ) {
        $this->compraPaqueteService = $compraPaqueteService;
        $this->paqueteService = $paqueteService;
        $this->billeteraService = $billeteraService;
        $this->billeteraTransaccionService = $billeteraTransaccionService;
        $this->parametroVigenciaService = $parametroVigenciaService;
        $this->causaService = $causaService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CompraPaquete::active();
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
        $compraPaquetes = $query->paginate($perPage);

        return new CompraPaqueteCollection($compraPaquetes);
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
    public function store(StoreCompraPaqueteRequest $request)
    {
        $fechaHoraSistema = Carbon::now('America/La_Paz')->format('Y-m-d H:i');
        $fechaActual = Carbon::now()->format('Y-m-d');
        $fechaFinalVigenciaGeneral = '';
        $idUser = Auth::id();
        $tipoUser = Auth::user()->tipo;
        $billetera = $this->billeteraService->obtenerUnoPorAbogadoId($idUser);
        //Validaciones para compra de paquete
        if (!$billetera || $billetera->monto < $request->monto) {
            return response()->json([
                'message' => 'No tiene suficiente saldo en su billetera general',
                'data' => null
            ], 409);
        }
        $paquete = $this->paqueteService->obtenerUno($request->paquete_id);
        if ($tipoUser != $paquete->tipo) {
            return response()->json([
                'message' => 'Este paquete es únicamente para usuarios ' . $paquete->tipo,
                'data' => null
            ], 409);
        }
        if ($paquete->tiene_fecha_limite === 1 &&  $fechaActual > $paquete->fecha_limite_compra) {
            return response()->json([
                'message' => 'Ya no puede comprar este paquete, fecha limite de compra era hasta ' . $paquete->fecha_limite_compra,
                'data' => null
            ], 409);
        }
        //Evaluacion EAPE
        if ($this->causaService->noPasoValidacionEAPEBilleteraGral($request->monto)) {
            return response()->json([
                'message' => 'ALERTA!
             Su solicitud no puede concretarse por falta de saldo en la billetera general. Por favor, agregue saldo y luego vuelva a intentarlo.',
                'data' => null
            ], 409);
        }

        DB::beginTransaction();
        try {
            /* obtener la fecha de vigencia general del usuario*/
            $parametroVigencia = $this->parametroVigenciaService->obtenerUnoPorUsuario($idUser);


            $fechaHora = Carbon::now('America/La_Paz')->toDateTimeString();
            //*Actualiza la fecha del parametro de vigencia
            if ($parametroVigencia->fecha_ultima_vigencia < $fechaHoraSistema) {
                $fechaInicioVigencia = Carbon::now('America/La_Paz');
                $fechaFinalVigencia = $fechaInicioVigencia->copy()->addDays($paquete->cantidad_dias);
                //Fecha vigencia general
                $fechaFinalVigenciaGeneral = $fechaFinalVigencia;
            } else { //Por falso se aumenta a la fecha general
                $fechaFinalVigenciaGeneralActual = Carbon::parse($parametroVigencia->fecha_ultima_vigencia); //parseo de ultima fecha vigencia
                $fechaFinalVigenciaGeneralActual->addMinute(); // Aumenta 1 minuto

                $fechaInicioVigencia = $fechaFinalVigenciaGeneralActual;
                $fechaFinalVigencia = $fechaInicioVigencia->copy()->addDays($paquete->cantidad_dias);

                $fechaFinalVigenciaGeneral = $fechaFinalVigencia; //* $nuevaFechaVigenciaGeneral;
            }
            $dataParametroVigencia = [
                'fecha_ultima_vigencia' => $fechaFinalVigenciaGeneral->format('Y-m-d H:i')
            ];
            $this->parametroVigenciaService->update($dataParametroVigencia, $parametroVigencia->id);

            //*Carga de datos
            $data = [
                'monto' => $request->monto,
                'fecha_ini_vigencia' => $fechaInicioVigencia->format('Y-m-d H:i'),
                'fecha_fin_vigencia' => $fechaFinalVigencia->format('Y-m-d H:i'),
                'fecha_compra' => $fechaHora,
                'dias_vigente' => $paquete->cantidad_dias,
                'paquete_id' => $request->paquete_id,
                'usuario_id' => $idUser,
            ];
            $compraPaquete = $this->compraPaqueteService->store($data);
            //Registro de transaccion en billetera
            $billeteraId = $billetera->id;
            $monto = $request->monto;
            $tipoTransaccion = TipoTransaccion::DEBITO;
            $glosa = GlosaTransaccion::DEBITO_POR_COMPRA_DEL_PAQUETE . " (" . $paquete->nombre . ")";
            $ordenId=0; //En este caso no existe
            $billeteraTransaccion = $this->billeteraTransaccionService->reistroTransaccionBilletera($billeteraId, $monto, $tipoTransaccion, $glosa, $ordenId);
            //Se activan las causas que estaban congeladas
            $causas= $this->causaService->activarEstadoPorUsuario($idUser);

            DB::commit();
            return response()->json([
                'message' => MessageHttp::CREADO_CORRECTAMENTE,
                'data' => $compraPaquete
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al comprar paquete: ' . $e->getMessage());

            return response()->json([
                'message' => MessageHttp::ERROR_CREAR,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CompraPaquete $compraPaquete)
    {
        $compraPaquete = $this->compraPaqueteService->obtenerUno($compraPaquete->id);
        return response()->json([
            'message' => MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data' => $compraPaquete
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CompraPaquete $compraPaquete)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CompraPaquete $compraPaquete)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CompraPaquete $compraPaquete)
    {
        //
    }
    public function listarActivosPorUsuario()
    {
        $compraPaquetes = $this->compraPaqueteService->listarActivosPorUsuario();
        $data = [
            'message' => MessageHttp::OBTENIDOS_CORRECTAMENTE,
            'data' => $compraPaquetes
        ];
        return response()->json($data);
    }
}
