<?php

namespace App\Http\Controllers;

use App\Constants\Estado;
use App\Enums\MessageHttp;
use Illuminate\Http\Request;
use App\Models\CuerpoExpediente;
use App\Http\Resources\CuerpoExpedienteCollection;
use App\Http\Requests\StoreCuerpoExpedienteRequest;
use App\Http\Requests\UpdateCuerpoExpedienteRequest;
use App\Models\Tribunal;
use App\Services\CuerpoExpedienteService;

class CuerpoExpedienteController extends Controller
{
    protected $cuerpoExpedienteService;

    public function __construct(CuerpoExpedienteService $cuerpoExpedienteService)
    {
        $this->cuerpoExpedienteService = $cuerpoExpedienteService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CuerpoExpediente::active();

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
        $cuerpoExpediente = $query->paginate($perPage);

        return new CuerpoExpedienteCollection($cuerpoExpediente);
    }

    public function listadoPorTribunal(Request $request, $tribunalId)
    {
        $query = CuerpoExpediente::active()
            ->where('tribunal_id', $tribunalId);
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
        $cuerpoExpediente = $query->paginate($perPage);

        return new CuerpoExpedienteCollection($cuerpoExpediente);
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
    public function store(StoreCuerpoExpedienteRequest $request)
    {
        //Preguntamos si esta enviando un archivo
        $tribunalId = $request->tribunal_id;
        $linkCuerpo = '';
        if ($request->hasFile('link_cuerpo')) {
            $tribunal = Tribunal::findOrFail($tribunalId);
            $file = $request->file('link_cuerpo');
            $linkCuerpo = $file->store('uploads/expedientes/causa-' . $tribunal->causa_id . '/tribunal-' . $tribunalId, 'public');
        } else {
            $linkCuerpo = $request->link_cuerpo;
        }
        $data = ([
            'nombre' => $request->nombre,
            'link_cuerpo' => $linkCuerpo,
            'tribunal_id' => $tribunalId
        ]);
        $cuerpoExpediente = $this->cuerpoExpedienteService->store($data);
        return response()
            ->json([
                'message' => MessageHttp::CREADO_CORRECTAMENTE,
                'data' => $cuerpoExpediente
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(CuerpoExpediente $cuerpoExpediente)
    {
        $data = [
            'message' => MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data' => $cuerpoExpediente
        ];
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CuerpoExpediente $cuerpoExpediente)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCuerpoExpedienteRequest $request, CuerpoExpediente $cuerpoExpediente)
    {
        $data = $request->only([
            'nombre',
            'tribunal_id',
        ]);
        //Preguntamos si esta enviando un archivo
        $tribunalId = $request->tribunal_id;
        $linkCuerpo = '';
        if ($request->hasFile('link_cuerpo')) {
            $tribunal = Tribunal::findOrFail($tribunalId);
            $file = $request->file('link_cuerpo');
            $linkCuerpo = $file->store('uploads/expedientes/causa-' . $tribunal->causa_id . '/tribunal-' . $tribunalId, 'public');
            $data['link_cuerpo'] = $linkCuerpo;
        } else {
            $linkCuerpo = $request->link_cuerpo;
            $data['link_cuerpo'] = $linkCuerpo;
        }
        $cuerpoExpediente = $this->cuerpoExpedienteService->update($data, $cuerpoExpediente->id);
        return response()->json([
            'message' => MessageHttp::ACTUALIZADO_CORRECTAMENTE,
            'data' => $cuerpoExpediente
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CuerpoExpediente $cuerpoExpediente)
    {
        $cuerpoExpediente = $this->cuerpoExpedienteService->destroy($cuerpoExpediente->id);
        return response()->json([
            'message' => MessageHttp::ELIMINADO_CORRECTAMENTE,
            'data' => $cuerpoExpediente
        ]);
    }
    public function listarExpedientesDigitalDeTribunal($tribunalId)
    {
        $cuerpoExpedientes = $this->cuerpoExpedienteService->listarExpedientesDigitalDeTribunal($tribunalId);
        return response()->json([
            'message' => MessageHttp::OBTENIDOS_CORRECTAMENTE,
            'data' => $cuerpoExpedientes
        ]);
    }
}
