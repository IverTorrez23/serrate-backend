<?php

namespace App\Http\Controllers;

use Exception;
use App\Constants\FechaHelper;
use App\Models\RegistroLlamada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Enums\MessageHttp;
use App\Http\Requests\StoreRegistroLlamadaRequest;
use App\Services\RegistroLlamadaService;

class RegistroLlamadaController extends Controller
{
    protected $registroLlamadaService;
    public function __construct(RegistroLlamadaService $registroLlamadaService)
    {
        $this->registroLlamadaService = $registroLlamadaService;
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
    public function store(StoreRegistroLlamadaRequest $request)
    {
        $idUser = Auth::user()->id;
        $fechaHora = FechaHelper::fechaHoraBolivia();
        DB::beginTransaction();
        try {
            $data = [
                'numero_telefono' => $request->numero_telefono,
                'fecha_llamada' => $fechaHora,
                'gestion_id' => $request->gestion_id,
                'usuario_id' => $idUser,
            ];
            $registroLlamada = $this->registroLlamadaService->store($data);
            DB::commit();
            return response()->json([
                'message' => MessageHttp::CREADO_CORRECTAMENTE,
                'data' => $registroLlamada
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error registrar llamada: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error registrar llamada',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(RegistroLlamada $registroLlamada)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RegistroLlamada $registroLlamada)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RegistroLlamada $registroLlamada)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RegistroLlamada $registroLlamada)
    {
        //
    }
    public function obtenerPorGestionId($gestionId)
    {
        $registroLlamadas = $this->registroLlamadaService->obtenerPorGestionId($gestionId);
        $data = [
            'message' => MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data' => $registroLlamadas
        ];
        return response()->json($data);
    }
}
