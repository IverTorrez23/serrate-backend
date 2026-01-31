<?php

namespace App\Http\Controllers;

use App\Constants\Estado;
use App\Enums\MessageHttp;
use Illuminate\Http\Request;
use App\Models\ClaseTribunal;
use App\Http\Resources\ClaseTribunalCollection;
use App\Http\Requests\StoreClaseTribunalRequest;
use App\Http\Requests\UpdateClaseTribunalRequest;
use App\Services\ClaseTribunalService;

class ClaseTribunalController extends Controller
{
    protected $claseTribunalService;

    public function __construct(ClaseTribunalService $claseTribunalService)
    {
        $this->claseTribunalService = $claseTribunalService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ClaseTribunal::active();

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
        $claseTribunal = $query->paginate($perPage);

        return new ClaseTribunalCollection($claseTribunal);
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
    public function store(StoreClaseTribunalRequest $request)
    {
        $data = ([
            'nombre' => $request->nombre,
        ]);
        $claseTribunal = $this->claseTribunalService->store($data);
        return response()
            ->json([
                'message' => MessageHttp::CREADO_CORRECTAMENTE,
                'data' => $claseTribunal
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(ClaseTribunal $claseTribunal)
    {
        $claseTribunal = $this->claseTribunalService->obtenerUno($claseTribunal->id);
        $data = [
            'message' => MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data' => $claseTribunal
        ];
        return response()->json($data);
    }
    public function listarActivos()
    {
        $claseTribunales = $this->claseTribunalService->listarActivos();
        $data = [
            'message' => MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data' => $claseTribunales
        ];
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ClaseTribunal $claseTribunal)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClaseTribunalRequest $request, ClaseTribunal $claseTribunal)
    {
        $claseTribunal->update($request->only([
            'nombre',
            'estado',
            'es_eliminado'
        ]));
        $data = [
            'message' => MessageHttp::ACTUALIZADO_CORRECTAMENTE,
            'data' => $claseTribunal
        ];
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClaseTribunal $claseTribunal)
    {
        $claseTribunal->es_eliminado   = 1;
        $claseTribunal->save();
        $data = [
            'message' => MessageHttp::ELIMINADO_CORRECTAMENTE,
            'data' => $claseTribunal
        ];
        return response()->json($data);
    }
}
