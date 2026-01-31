<?php

namespace App\Http\Controllers;

use Exception;
use App\Constants\Estado;
use App\Enums\MessageHttp;
use App\Models\CausaPosta;
use Illuminate\Http\Request;
use App\Http\Resources\CausaPostaCollection;
use App\Http\Requests\StoreCausaPostaRequest;
use App\Http\Requests\UpdateCausaPostaRequest;
use App\Services\CausaPostaService;
use App\Services\CausaService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CausaPostaController extends Controller
{
    protected $causaPostaService;
    protected $causaService;
    public function __construct(CausaPostaService $causaPostaService, CausaService $causaService)
    {
        $this->causaPostaService = $causaPostaService;
        $this->causaService = $causaService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $causaPosta = CausaPosta::where('es_eliminado', 0)
            ->where('estado', Estado::ACTIVO)
            ->paginate();
        return new CausaPostaCollection($causaPosta);
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
    public function store(StoreCausaPostaRequest $request)
    {
        $causaPosta = CausaPosta::create([
            'nombre' => $request->nombre,
            'numero_posta' => $request->numero_posta,
            'copia_nombre_plantilla' => $request->copia_nombre_plantilla,
            'tiene_informe' => 0,
            'causa_id' => $request->causa_id,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0
        ]);
        $data = [
            'message' => MessageHttp::CREADO_CORRECTAMENTE,
            'data' => $causaPosta
        ];
        return response()
            ->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(CausaPosta $causaPosta)
    {
        $data = [
            'message' => MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data' => $causaPosta
        ];
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CausaPosta $causaPosta)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCausaPostaRequest $request, CausaPosta $causaPosta)
    {
        $causaPosta->update($request->only([
            'nombre',
            'numero_posta',
            'copia_nombre_plantilla',
            'tiene_informe',
            'causa_id',
            'estado',
            'es_eliminado'
        ]));
        $data = [
            'message' => MessageHttp::ACTUALIZADO_CORRECTAMENTE,
            'data' => $causaPosta
        ];
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CausaPosta $causaPosta)
    {
        $causaPosta->es_eliminado   = 1;
        $causaPosta->save();
        $data = [
            'message' => MessageHttp::ELIMINADO_CORRECTAMENTE,
            'data' => $causaPosta
        ];
        return response()->json($data);
    }
    public function listadoActivosPorCausa($causaId)
    {
        $causaPostas = $this->causaPostaService->listadoActivosPorCausa($causaId);
        $data = [
            'message' => MessageHttp::OBTENIDOS_CORRECTAMENTE,
            'data' => $causaPostas
        ];
        return response()->json($data);
    }
    public function eliminarTodoPorCausa($causaId)
    {
        DB::beginTransaction();
        try {
            $eliminados = $this->causaPostaService->eliminarTodoPorCausa($causaId);
            $dataCausa = [
                'plantilla_id' => 0
            ];
            $causa = $this->causaService->update($dataCausa, $causaId);

            DB::commit();
            return response()->json([
                'message' => MessageHttp::ELIMINADO_CORRECTAMENTE,
                'data' => $eliminados
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error eliminar avance: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error eliminar avance  fisico',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
