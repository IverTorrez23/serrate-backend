<?php

namespace App\Http\Controllers;

use App\Models\SugerenciaOrden;
use App\Services\SugerenciaOrdenService;
use Illuminate\Http\Request;



class SugerenciaOrdenController extends Controller
{
    protected $sugerenciaOrdenService;

    public function __construct(SugerenciaOrdenService $sugerenciaOrdenService)
    {
        $this->sugerenciaOrdenService = $sugerenciaOrdenService;
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
    public function show(SugerenciaOrden $sugerenciaOrden)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SugerenciaOrden $sugerenciaOrden)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SugerenciaOrden $sugerenciaOrden)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SugerenciaOrden $sugerenciaOrden)
    {
        //
    }
    public function listarPorOrden($ordenId)
    {
        try {
            $sugerencias = $this->sugerenciaOrdenService->listarSugerenciasParaUnaOrden($ordenId);
            return response()->json([
                'message' => 'sugerencias orden obtenidos correctamente',
                'data'    => $sugerencias
            ], 200);
        } catch (\Throwable $e) {

            return response()->json([
                'status'  => 'error',
                'message' => 'Error al obtener sugerencias orden.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
                'line'    => config('app.debug') ? $e->getLine() : null,
                'file'    => config('app.debug') ? $e->getFile() : null,
            ], 500);
        }
    }
}
