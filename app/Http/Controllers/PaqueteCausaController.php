<?php

namespace App\Http\Controllers;

use App\Constants\EstadoCausa;
use Exception;
use Carbon\Carbon;
use App\Enums\MessageHttp;
use App\Models\PaqueteCausa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\PaqueteCausaService;
use App\Services\CompraPaqueteService;
use App\Http\Requests\StorePaqueteCausaRequest;
use App\Services\CausaService;

class PaqueteCausaController extends Controller
{
    protected $paqueteCausaService;
    protected $compraPaqueteService;
    protected $causaService;

    public function __construct(PaqueteCausaService $paqueteCausaService, CompraPaqueteService $compraPaqueteService, CausaService $causaService)
    {
        $this->paqueteCausaService = $paqueteCausaService;
        $this->compraPaqueteService = $compraPaqueteService;
        $this->causaService = $causaService;
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
    public function store(StorePaqueteCausaRequest $request)
    {
        DB::beginTransaction();
        try {
            $compraPaqueteId = $request->compra_paquete_id;
            $causaId = $request->causa_id;

            if ($this->compraPaqueteService->isCompraPaqueteAgotado($compraPaqueteId)) {
                return response()->json([
                    'message' => 'Cupo de Paquete agotado: ya no puede incluir mas causas a este paquete',
                    'data' => null
                ], 409);
            }
            if ($this->compraPaqueteService->isCompraPaqueteExpirado($compraPaqueteId)) {
                return response()->json([
                    'message' => 'Paquete expirado: este paquete ya no esta vigente',
                    'data' => null
                ], 409);
            }
            if($this->paqueteCausaService->causaEstaEnPaquete($causaId)){
                return response()->json([
                    'message' => 'La causa con ID: '.$causaId.' ya esta en un paquete',
                    'data' => null
                ], 409);
            }
            $fechaHora = Carbon::now('America/La_Paz')->toDateTimeString();
            $compraPaquete = $this->compraPaqueteService->obtenerUno($compraPaqueteId);
            $data = [
                'fecha_inicio' => $compraPaquete->fecha_ini_vigencia,
                'fecha_fin' => $compraPaquete->fecha_fin_vigencia,
                'compra_paquete_id' => $compraPaqueteId,
                'causa_id' => $causaId,
                'fecha_asociacion' => $fechaHora,
                'usuario_id' => Auth::user()->id
            ];

            $paqueteCausa = $this->paqueteCausaService->store($data);
            //La causa que se le asigna un paquete su esta cambia a activa
            $dataCausa['estado'] = EstadoCausa::ACTIVA;
            $causa = $this->causaService->update($dataCausa, $causaId);

            DB::commit();
            return response()->json([
                'message' => MessageHttp::CREADO_CORRECTAMENTE,
                'data' => $paqueteCausa
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

    /**
     * Display the specified resource.
     */
    public function show(PaqueteCausa $paqueteCausa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PaqueteCausa $paqueteCausa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PaqueteCausa $paqueteCausa)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaqueteCausa $paqueteCausa)
    {
        $paqueteCausa = $this->paqueteCausaService->destroy($paqueteCausa);
        //Cuando la causa se quita de un paquete, esta se pone CONGELADA
        $dataCausa['estado'] = EstadoCausa::CONGELADA;
        $causa = $this->causaService->update($dataCausa, $paqueteCausa->causa_id);
        return response()->json([
            'message' => MessageHttp::ELIMINADO_CORRECTAMENTE,
            'data' => $paqueteCausa
        ]);
    }
    public function listadoActivosDeUnPaquete($compraPaqueteId)
    {
        $paqueteCausas = $this->paqueteCausaService->listadoActivosDeUnPaquete($compraPaqueteId);
        return response()->json([
            'message' => MessageHttp::OBTENIDOS_CORRECTAMENTE,
            'data' => $paqueteCausas
        ]);
    }
}
