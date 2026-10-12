<?php

namespace App\Http\Controllers;

use Exception;
use App\Enums\MessageHttp;
use App\Http\Requests\StoreSugerenciaGastoRequest;
use App\Http\Requests\UpdateSugerenciaGastoRequest;
use App\Http\Resources\TransaccionesCausaCollection;
use App\Models\SugerenciaGasto;
use App\Services\SugerenciaGastoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SugerenciaGastoController extends Controller
{
    protected $sugerenciaGastoService;

    public function __construct(SugerenciaGastoService $sugerenciaGastoService)
    {
        $this->sugerenciaGastoService = $sugerenciaGastoService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $data = $this->sugerenciaGastoService->obtenerSugerenciaGasto($request);
            return new TransaccionesCausaCollection($data);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las sugerencia de gastos.',
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
    public function store(StoreSugerenciaGastoRequest $request)
    {
        DB::beginTransaction();
        try {
            $sugerencia = [
                'detalle' => $request->detalle,
                'monto' => $request->monto,
                'orden_id' => $request->orden_id,
                'usuario_id' => Auth::user()->id
            ];
            $sugerencia = $this->sugerenciaGastoService->store($sugerencia);

            DB::commit();
            return response()->json([
                'message' => MessageHttp::CREADO_CORRECTAMENTE,
                'data' => $sugerencia
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error registrar sugerencia: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error registrar sugerencia',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SugerenciaGasto $sugerenciaGasto)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SugerenciaGasto $sugerenciaGasto)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSugerenciaGastoRequest $request, SugerenciaGasto $sugerenciaGasto)
    {
        DB::beginTransaction();
        try {
            $data = $request->only([
                'detalle',
                'monto'
            ]);
            $sugerencia = $this->sugerenciaGastoService->update($data, $sugerenciaGasto->id);
            DB::commit();
            return response()->json([
                'message' => MessageHttp::ACTUALIZADO_CORRECTAMENTE,
                'data' => $sugerencia
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error actualizar sugerencia: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error actualizar sugerencia',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SugerenciaGasto $sugerenciaGasto)
    {
        DB::beginTransaction();
        try {

            $sugerencia = $this->sugerenciaGastoService->destroy($sugerenciaGasto);
            DB::commit();
            return response()->json([
                'message' => MessageHttp::ELIMINADO_CORRECTAMENTE,
                'data' => $sugerencia
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error eliminar sugerencia: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error eliminar sugerencia',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function listarActivosYConOrden($ordenId)
    {
        try {
            $sugerencias = $this->sugerenciaGastoService->listarActivosYConUnaOrden($ordenId);
            return response()->json([
                'message' => 'sugerencias de orden obtenidos correctamente',
                'data'    => $sugerencias
            ], 200);
        } catch (\Throwable $e) {

            return response()->json([
                'status'  => 'error',
                'message' => 'Error al obtener sugerencias.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
                'line'    => config('app.debug') ? $e->getLine() : null,
                'file'    => config('app.debug') ? $e->getFile() : null,
            ], 500);
        }
    }
    public function listarActivosGenerales()
    {
        try {
            $sugerencias = $this->sugerenciaGastoService->listarActivosGenerales();
            return response()->json([
                'message' => 'sugerencias generales obtenidos correctamente',
                'data'    => $sugerencias
            ], 200);
        } catch (\Throwable $e) {

            return response()->json([
                'status'  => 'error',
                'message' => 'Error al obtener sugerencias generales.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
                'line'    => config('app.debug') ? $e->getLine() : null,
                'file'    => config('app.debug') ? $e->getFile() : null,
            ], 500);
        }
    }
}
