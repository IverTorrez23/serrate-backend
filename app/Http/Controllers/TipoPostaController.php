<?php

namespace App\Http\Controllers;

use App\Constants\Estado;
use App\Models\TipoPosta;
use App\Enums\MessageHttp;
use Illuminate\Http\Request;
use App\Http\Resources\TipoPostaCollection;
use App\Http\Requests\StoreTipoPostaRequest;
use App\Http\Requests\UpdateTipoPostaRequest;
use App\Services\TipoPostaService;

class TipoPostaController extends Controller
{
    protected $tipoPostaService;
    public function __construct(TipoPostaService $tipoPostaService)
    {
        $this->tipoPostaService = $tipoPostaService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TipoPosta::active();

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
        $tipoPostas = $query->paginate($perPage);

        return new TipoPostaCollection($tipoPostas);
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
    public function store(StoreTipoPostaRequest $request)
    {
        $tipoPosta = TipoPosta::create([
            'nombre' => $request->nombre,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0
        ]);
        $data = [
            'message' => MessageHttp::CREADO_CORRECTAMENTE,
            'data' => $tipoPosta
        ];
        return response()
            ->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(TipoPosta $tipoPosta)
    {
        $data = [
            'message' => MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data' => $tipoPosta
        ];
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TipoPosta $tipoPosta)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTipoPostaRequest $request, TipoPosta $tipoPosta)
    {
        $tipoPosta->update($request->only([
            'nombre',
            'estado',
            'es_eliminado'
        ]));
        $data = [
            'message' => MessageHttp::ACTUALIZADO_CORRECTAMENTE,
            'data' => $tipoPosta
        ];
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TipoPosta $tipoPosta)
    {
        $tipoPosta->es_eliminado   = 1;
        $tipoPosta->save();
        $data = [
            'message' => MessageHttp::ELIMINADO_CORRECTAMENTE,
            'data' => $tipoPosta
        ];
        return response()->json($data);
    }
    public function listarActivos()
    {
        $tipoPostas = $this->tipoPostaService->listarActivos();
        $data = [
            'message' => MessageHttp::OBTENIDOS_CORRECTAMENTE,
            'data' => $tipoPostas
        ];
        return response()->json($data);
    }
}
