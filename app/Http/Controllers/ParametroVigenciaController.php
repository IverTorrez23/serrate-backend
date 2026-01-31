<?php

namespace App\Http\Controllers;

use App\Models\ParametroVigencia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Enums\MessageHttp;
use App\Services\ParametroVigenciaService;

class ParametroVigenciaController extends Controller
{
    protected $parametroVigenciaService;

    public function __construct(ParametroVigenciaService $parametroVigenciaService)
    {
        $this->parametroVigenciaService = $parametroVigenciaService;
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
    public function show(ParametroVigencia $parametroVigencia)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ParametroVigencia $parametroVigencia)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ParametroVigencia $parametroVigencia)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ParametroVigencia $parametroVigencia)
    {
        //
    }
    public function obtenerUnoUsuario()
    {
        $idUser = Auth::id();
        $parametroVigencia = $this->parametroVigenciaService->obtenerUnoPorUsuario($idUser);
        $data = [
            'message' => MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data' => $parametroVigencia
        ];
        return response()->json($data);
    }
}
