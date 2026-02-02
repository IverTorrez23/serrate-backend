<?php

namespace App\Http\Controllers;

use App\Constants\GlosaTransaccion;
use App\Constants\TipoTransaccion;
use App\Constants\Transaccion;
use App\Constants\TipoUsuario;
use Exception;
use Carbon\Carbon;
use App\Enums\MessageHttp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\BilleteraTransaccion;
use App\Http\Resources\BilleteraTransaccionCollection;
use App\Http\Requests\StoreBilleteraTransaccionRequest;
use App\Services\BilleteraService;
use App\Services\BilleteraTransaccionService;
use App\Services\TransaccionesAdminService;

class BilleteraTransaccionController extends Controller
{
    protected $billeteraService;
    protected $billeteraTransaccionService;
    protected $transaccionesAdminService;

    public function __construct(BilleteraService $billeteraService, BilleteraTransaccionService $billeteraTransaccionService, TransaccionesAdminService $transaccionesAdminService)
    {
        $this->billeteraService = $billeteraService;
        $this->billeteraTransaccionService = $billeteraTransaccionService;
        $this->transaccionesAdminService = $transaccionesAdminService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BilleteraTransaccion::active();

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
        $billeteraTransaccion = $query->paginate($perPage);

        return new BilleteraTransaccionCollection($billeteraTransaccion);
    }
    public function listadoPorBilletera(Request $request, $fechaIni, $fechaFin, $billeteraId)
    {
        $ini = Carbon::parse(urldecode($fechaIni))->startOfDay();   // 2025-10-18 00:00:00
        $fin = Carbon::parse(urldecode($fechaFin))->endOfDay();     // 2025-10-18 23:59:59
        $query = BilleteraTransaccion::active()
            ->where('billetera_id', $billeteraId)
            ->whereBetween('fecha_transaccion', [$ini, $fin]);

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
        $billeteraTransaccion = $query->paginate($perPage);
        return new BilleteraTransaccionCollection($billeteraTransaccion);
    }
    public function DepBilleteraDesdeAdmin(Request $request)
    {
        
        $query = BilleteraTransaccion::active()
            ->where('glosa', GlosaTransaccion::CREDITO_DEPOSITO_A_BILLETERA_FROM_ADMIN);
            

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
        $billeteraTransaccion = $query->paginate($perPage);
        return new BilleteraTransaccionCollection($billeteraTransaccion);
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
    public function store(StoreBilleteraTransaccionRequest $request)
    {
        if (Auth::user()->tipo != TipoUsuario::ABOGADO_INDEPENDIENTE && Auth::user()->tipo != TipoUsuario::ABOGADO_LIDER && Auth::user()->tipo != TipoUsuario::ADMINISTRADOR) {
            return response()->json([
                'message' => 'Usted no tiene permiso para esta acción',
                'data' => null
            ], 403); // Código 403 para "Prohibido"
        }
        DB::beginTransaction();
        try {
            if(Auth::user()->tipo == TipoUsuario::ADMINISTRADOR){
                $glosaTrnBill = GlosaTransaccion::CREDITO_DEPOSITO_A_BILLETERA_FROM_ADMIN;
            }else{
                $glosaTrnBill = GlosaTransaccion::CREDITO_DEPOSITO_A_BILLETERA;
            }
            $fechaHora = Carbon::now('America/La_Paz')->toDateTimeString();
            $billeteraId = $request->billetera_id;
            $monto = $request->monto;
            $tipoTransaccion = TipoTransaccion::CREDITO;
            $glosa = $glosaTrnBill;
            $ordenId = 0; //En este caso es cero
            $billeteraTransaccion = $this->billeteraTransaccionService->reistroTransaccionBilletera($billeteraId, $monto, $tipoTransaccion, $glosa, $ordenId);
            //TransaccionAdmin
            $dataTrnAdmin = [
                'monto' => $monto,
                'fecha_transaccion' => $fechaHora,
                'tipo' => TipoTransaccion::CREDITO,
                'transaccion' => Transaccion::INGRESO_POR_DEPOSITO_ABOGADO,
                'glosa' => GlosaTransaccion::CREDITO_POR_DEPOSITO_ABOGADO,
                'usuario_id' => Auth::user()->id,
                'billetera_id' => $request->billetera_id
            ];
            $transaccionesAdmin = $this->transaccionesAdminService->registrarTransaccionAdmin($dataTrnAdmin);

            DB::commit();
            return response()->json([
                'message' => MessageHttp::CREADO_CORRECTAMENTE,
                'data' => $billeteraTransaccion
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

    /**
     * Display the specified resource.
     */
    public function show(BilleteraTransaccion $billeteraTransaccion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BilleteraTransaccion $billeteraTransaccion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BilleteraTransaccion $billeteraTransaccion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BilleteraTransaccion $billeteraTransaccion)
    {
        $billeteraTransaccion = $this->billeteraTransaccionService->destroy($billeteraTransaccion->id);
        return response()->json([
            'message' => MessageHttp::ELIMINADO_CORRECTAMENTE,
            'data' => $billeteraTransaccion
        ]);
    }
}
