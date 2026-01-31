<?php

namespace App\Http\Controllers;

use App\Constants\TipoDocumento;
use App\Enums\MessageHttp;
use App\Http\Requests\StoreDocumentosCategoriaRequest;
use App\Http\Requests\UpdateDocumentosCategoriaRequest;
use App\Http\Resources\DocumentoCategoriaCollection;
use App\Models\DocumentosCategoria;
use App\Services\DocumentosCategoriaService;
use Illuminate\Http\Request;

class DocumentosCategoriaController extends Controller
{
    protected $documentosCategoriaService;

    public function __construct(DocumentosCategoriaService $documentosCategoriaService)
    {
        $this->documentosCategoriaService = $documentosCategoriaService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DocumentosCategoria::active();

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
        $documentosCategorias = $query->paginate($perPage);
        $documentosCategorias->load('padre');

        return new DocumentoCategoriaCollection($documentosCategorias);
    }

    public function indexTramites(Request $request)
    {
        $query = DocumentosCategoria::active()
            ->where('tipo', TipoDocumento::TRAMITES);

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
        $documentosCategorias = $query->paginate($perPage);
        $documentosCategorias->load('padre');

        return new DocumentoCategoriaCollection($documentosCategorias);
    }

    public function indexNormas(Request $request)
    {
        $query = DocumentosCategoria::active()
            ->where('tipo', TipoDocumento::NORMAS);

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
        $documentosCategorias = $query->paginate($perPage);
        $documentosCategorias->load('padre');

        return new DocumentoCategoriaCollection($documentosCategorias);
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
    public function store(StoreDocumentosCategoriaRequest $request)
    {
        $data = [
            'nombre' => $request->nombre,
            'tipo' => $request->tipo
        ];
        $data['categoria_id'] = $request->has('categoria_id') ? $request->categoria_id : 0;
        $documentosCategoria = $this->documentosCategoriaService->store($data);

        return response()->json([
            'message' => MessageHttp::CREADO_CORRECTAMENTE,
            'data' => $documentosCategoria
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(DocumentosCategoria $documentosCategoria = null)
    {
        if ($documentosCategoria) {
            $data = [
                'message' => MessageHttp::OBTENIDO_CORRECTAMENTE,
                'data' => $documentosCategoria
            ];
        } else {
            $documentosCategorias = $this->documentosCategoriaService->listarActivos();
            $data = [
                'message' => MessageHttp::OBTENIDOS_CORRECTAMENTE,
                'data' => $documentosCategorias
            ];
        }
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DocumentosCategoria $documentosCategoria)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDocumentosCategoriaRequest $request, DocumentosCategoria $documentosCategoria)
    {
        $data = $request->only([
            'nombre',
            'tipo',
            'categoria_id'
        ]);
        $documentosCategoria = $this->documentosCategoriaService->update($data, $documentosCategoria->id);
        return response()->json([
            'message' => MessageHttp::ACTUALIZADO_CORRECTAMENTE,
            'data' => $documentosCategoria
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DocumentosCategoria $documentosCategoria)
    {
        $documentosCategoria->es_eliminado   = 1;
        $documentosCategoria->save();
        $data = [
            'message' => MessageHttp::ELIMINADO_CORRECTAMENTE,
            'data' => $documentosCategoria
        ];
        return response()->json($data);
    }
    public function listarSubcategorias(DocumentosCategoria $documentosCategoria)
    {
        $documentosCategorias = $this->documentosCategoriaService->listarSubcategorias($documentosCategoria->id);
        $data = [
            'message' => MessageHttp::OBTENIDOS_CORRECTAMENTE,
            'data' => $documentosCategorias
        ];
        return response()->json($data);
    }
    public function listarCategorias()
    {
        $documentosCategorias = $this->documentosCategoriaService->listarCategorias();
        $data = [
            'message' => MessageHttp::OBTENIDOS_CORRECTAMENTE,
            'data' => $documentosCategorias
        ];
        return response()->json($data);
    }
    public function listarCategoriasTramites()
    {
        $documentosCategorias = $this->documentosCategoriaService->listarCategoriasTramites();
        $data = [
            'message' => MessageHttp::OBTENIDOS_CORRECTAMENTE,
            'data' => $documentosCategorias
        ];
        return response()->json($data);
    }
    public function listarCategoriasNormas()
    {
        $documentosCategorias = $this->documentosCategoriaService->listarCategoriasNormas();
        $data = [
            'message' => MessageHttp::OBTENIDOS_CORRECTAMENTE,
            'data' => $documentosCategorias
        ];
        return response()->json($data);
    }
}
