<?php

namespace App\Http\Controllers;

use Exception;
use App\Constants\FechaHelper;
use App\Constants\GlosaTransaccion;
use App\Constants\TipoTransaccion;
use App\Constants\Transaccion;
use App\Enums\MessageHttp;
use Illuminate\Http\Request;
use App\Models\DevolucionSaldo;
use App\Http\Resources\DevolucionSaldoCollection;
use App\Http\Requests\StoreDevolucionSaldoRequest;
use App\Models\TablaConfig;
use App\Services\BilleteraService;
use App\Services\BilleteraTransaccionService;
use App\Services\CausaService;
use App\Services\DevolucionSaldoService;
use App\Services\OrdenService;
use App\Services\TransaccionesAdminService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DevolucionSaldoController extends Controller
{
    protected $devolucionSaldoService;
    protected $billeteraService;
    protected $ordenService;
    protected $causaService;
    protected $transaccionesAdminService;
    protected $billeteraTransaccionService;

    public function __construct(DevolucionSaldoService $devolucionSaldoService, BilleteraService $billeteraService, OrdenService $ordenService, CausaService $causaService, TransaccionesAdminService $transaccionesAdminService, BilleteraTransaccionService $billeteraTransaccionService)
    {
        $this->devolucionSaldoService = $devolucionSaldoService;
        $this->billeteraService = $billeteraService;
        $this->ordenService = $ordenService;
        $this->causaService = $causaService;
        $this->transaccionesAdminService = $transaccionesAdminService;
        $this->billeteraTransaccionService = $billeteraTransaccionService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $data = $this->devolucionSaldoService->obtenerDevoluciones($request);
            return new DevolucionSaldoCollection($data);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las devoluciones.',
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
    public function store(StoreDevolucionSaldoRequest $request)
    {
        $glosa = $request->glosa;
        $monto = $request->monto;
        $billetera_id = $request->billetera_id;
        $fechaHora = FechaHelper::fechaHoraBolivia();

        $billetera = $this->billeteraService->obtenerUno($billetera_id);
        $userAbogId = $billetera->abogado_id;

        if ($this->causaService->usuarioTieneCausasNoTerminadas($userAbogId)) {
            return response()->json([
                'message' => 'No puede hacer la devolución porque el usuario tiene causas sin terminar',
                'data' => null
            ], 409);
        }
        if ($this->causaService->usuarioTieneCausasConSaldo($userAbogId)) {
            return response()->json([
                'message' => 'No puede hacer la devolución porque el usuario tiene causas con saldo',
                'data' => null
            ], 409);
        }
        
        if ($monto != $billetera->monto) {
            return response()->json([
                'message' => 'El monto a devolver no coincide con el saldo de la billetera general, recargue la pagina por favor monto:'.$monto.' biletera;'.$billetera->monto,
                'data' => null
            ], 409);
        }

        if ($this->ordenService->usuarioTieneOrdenesNoCerradas($userAbogId)) {
            return response()->json([
                'message' => 'No puede hacer la devolución porque el usuario tiene ordenes sin cerrar',
                'data' => null
            ], 409);
        }

        $tablaConfig = TablaConfig::findOrFail(1);
        if ($monto > $tablaConfig->caja_admin) {
            return response()->json([
                'message' => 'Usted no tiene suficiente saldo para realizar esta acción',
                'data' => null
            ], 409);
        }
        if ($monto === 0) {
            return response()->json([
                'message' => 'El monto a devolver es cero, no puede devolver cero',
                'data' => null
            ], 409);
        }
        DB::beginTransaction();
        try {

            $devolucionData = [
                'fecha_devolucion' => $fechaHora,
                'glosa' => $glosa,
                'monto' => $monto,
                'billetera_id' => $billetera_id,
                'usuario_id' => Auth::user()->id
            ];
            $devolucion = $this->devolucionSaldoService->store($devolucionData);
            //Transaccion en caja admin
            $dataTrnAdmin = [
                'monto' => $monto,
                'fecha_transaccion' => $fechaHora,
                'tipo' => TipoTransaccion::DEBITO,
                'transaccion' => Transaccion::EGRESO_POR_DEVOLUCION_ABOGADO,
                'glosa' => GlosaTransaccion::DEBITO_POR_DEVOLUCION_SALDO,
                'usuario_id' => Auth::user()->id,
            ];
            $trnAdmin = $this->transaccionesAdminService->registrarTransaccionAdmin($dataTrnAdmin);
            //Transaccion en billetera
            $tipoTransaccion = TipoTransaccion::DEBITO;
            $glosaTrnB = GlosaTransaccion::DEBITO_POR_DEVOLUCION_SALDO_BILLETERA_GRAL;
            $ordenId = 0; //En este caso es cero
            $billeteraTransaccion = $this->billeteraTransaccionService->reistroTransaccionBilletera($billetera_id, $monto, $tipoTransaccion, $glosaTrnB, $ordenId);



            DB::commit();
            return response()->json([
                'message' => MessageHttp::CREADO_CORRECTAMENTE,
                'data' => $devolucion
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
    public function show(DevolucionSaldo $devolucionSaldo)
    {
        $data = [
            'message' => MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data' => $devolucionSaldo
        ];
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DevolucionSaldo $devolucionSaldo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DevolucionSaldo $devolucionSaldo)
    {
        $devolucionSaldo->update($request->only([
            'detalle_devolucion',
            'monto',
            'causa_id',
            'estado',
            'es_eliminado'
        ]));
        $data = [
            'message' => MessageHttp::ACTUALIZADO_CORRECTAMENTE,
            'data' => $devolucionSaldo
        ];
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DevolucionSaldo $devolucionSaldo)
    {
        $devolucionSaldo->es_eliminado   = 1;
        $devolucionSaldo->save();
        $data = [
            'message' => MessageHttp::ELIMINADO_CORRECTAMENTE,
            'data' => $devolucionSaldo
        ];
        return response()->json($data);
    }
}
