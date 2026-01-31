<?php

namespace App\Http\Controllers;

use App\Models\Billetera;
use App\Enums\MessageHttp;
use Illuminate\Http\Request;
use App\Services\BilleteraService;

class BilleteraController extends Controller
{
    protected $billeteraService;

    public function __construct(BilleteraService $billeteraService)
    {
        $this->billeteraService = $billeteraService;
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
    public function show(Billetera $billetera)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Billetera $billetera)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Billetera $billetera)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Billetera $billetera)
    {
        //
    }
    public function obtenerPorAbogadoId($abogadoId)
    {
        $billetera = $this->billeteraService->obtenerUnoPorAbogadoId($abogadoId);
        $data=[
            'message'=> MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data'=>$billetera
        ];
        return response()->json($data);
    }
    public function listarConUsuarios()
    {
        $billetera = $this->billeteraService->listarConUsuarios();
        $data=[
            'message'=> MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data'=>$billetera
        ];
        return response()->json($data);
    }
}
