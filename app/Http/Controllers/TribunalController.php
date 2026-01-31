<?php

namespace App\Http\Controllers;

use App\Models\Tribunal;
use App\Constants\Estado;
use App\Enums\MessageHttp;
use Illuminate\Http\Request;
use App\Http\Resources\TribunalCollection;
use App\Http\Requests\StoreTribunalRequest;
use App\Http\Requests\UpdateTribunalRequest;
use App\Models\Causa;
use App\Services\TribunalService;

class TribunalController extends Controller
{
    protected $tribunalService;

    public function __construct(TribunalService $tribunalService)
    {
        $this->tribunalService = $tribunalService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Tribunal::active();

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
        $tribunales = $query->paginate($perPage);

        return new TribunalCollection($tribunales);
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
    public function store(StoreTribunalRequest $request)
    {
        if ($request->tribunal_dominante === 1){
            $this->tribunalService->desmarcarTribunaDominanteDeCausa($request->causa_id);
        }
        $data = ([
            'expediente' => $request->expediente,
            'codnurejianuj' => $request->codnurejianuj,
            'link_carpeta' => '', //$request->link_carpeta,
            'clasetribunal_id' => $request->clasetribunal_id,
            'causa_id' => $request->causa_id,
            'juzgado_id' => $request->juzgado_id,
            'tribunal_dominante' => $request->tribunal_dominante,
        ]);
        $tribunal = $this->tribunalService->store($data);

        return response()
            ->json([
                'message' => MessageHttp::CREADO_CORRECTAMENTE,
                'data' => $tribunal
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Tribunal $tribunal)
    {
        $tribunal = $this->tribunalService->obtenerUno($tribunal->id);
        $data = [
            'message' => MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data' => $tribunal
        ];
        return response()->json($data);
    }

    public function listarActivosPorCausa($causaId)
    {
        $tribunales = $this->tribunalService->listarActivosPorCausa($causaId);
       
        $data = [
            'message' => MessageHttp::OBTENIDOS_CORRECTAMENTE,
            'data' => $tribunales
        ];
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tribunal $tribunal)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTribunalRequest $request, Tribunal $tribunal)
    {
        if ($request->tribunal_dominante === 1){
            $this->tribunalService->desmarcarTribunaDominanteDeCausa($request->causa_id);
        }
        $data = $request->only([
            'expediente',
            'codnurejianuj',
            'link_carpeta',
            'clasetribunal_id',
            'causa_id',
            'juzgado_id',
            'tribunal_dominante',
            'estado',
            'es_eliminado'
        ]);
        $tribunal = $this->tribunalService->update($data, $tribunal->id);

        return response()->json([
            'message' => MessageHttp::ACTUALIZADO_CORRECTAMENTE,
            'data' => $tribunal
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tribunal $tribunal)
    {
        $tribunal = $this->tribunalService->destroy($tribunal);
        $data = [
            'message' => MessageHttp::ELIMINADO_CORRECTAMENTE,
            'data' => $tribunal
        ];
        return response()->json($data);
    }
    public function listarPorCausaId($causaId)
    {
        $tribunales = $this->tribunalService->listarPorCausaId($causaId);
        $data = [
            'message' => MessageHttp::OBTENIDOS_CORRECTAMENTE,
            'data' => $tribunales
        ];
        return response()->json($data);
    }
}
