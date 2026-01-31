<?php

namespace App\Http\Controllers;

use App\Constants\TipoDocumento;
use App\Enums\MessageHttp;
use App\Http\Requests\UpdateDocumentoRequest;
use App\Http\Resources\DocumentoCollection;
use App\Models\Documento;
use App\Services\DocumentoService;
use Illuminate\Http\Request;

class DocumentoController extends Controller
{
    protected $documentoService;

    public function __construct(DocumentoService $documentoService)
    {
        $this->documentoService = $documentoService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Documento::active();

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
        $documentos = $query->paginate($perPage);
        $documentos->load('categoria');

        return new DocumentoCollection($documentos);
    }

    public function indexTramites(Request $request)
    {
        $query = Documento::active()
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
        $documentos = $query->paginate($perPage);
        $documentos->load('categoria');

        return new DocumentoCollection($documentos);
    }

    public function indexNormas(Request $request)
    {
        $query = Documento::active()
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
        $documentos = $query->paginate($perPage);
        $documentos->load('categoria');

        return new DocumentoCollection($documentos);
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
        if ($request->hasFile('archivo_url')) {
            $file = $request->file('archivo_url');
            if ($request->tipo === TipoDocumento::NORMAS) {
                $path = $file->store('uploads/pdf/NORMAS', 'public');
            } else {
                if ($request->tipo === TipoDocumento::TRAMITES) {
                    $path = $file->store('uploads/pdf/TRAMITES', 'public');
                }
            }
        } else {
            $path = null;
        }

        $data = [
            'nombre' => $request->nombre,
            'archivo_url' => $path,
            'tipo' => $request->tipo,
            'categoria_id' => $request->categoria_id,
        ];
        $documento = $this->documentoService->store($data);
        return response()
            ->json([
                'message' => MessageHttp::CREADO_CORRECTAMENTE,
                'data' => $documento
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Documento $documento)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Documento $documento)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDocumentoRequest $request, Documento $documento)
    {
        $data = $request->only([
            'nombre',
            'tipo',
            'categoria_id'
        ]);
        if ($request->hasFile('archivo_url')) {
            $file = $request->file('archivo_url');
            if ($request->tipo === TipoDocumento::NORMAS) {
                $path = $file->store('uploads/pdf/NORMAS', 'public');
            } else {
                if ($request->tipo === TipoDocumento::TRAMITES) {
                    $path = $file->store('uploads/pdf/TRAMITES', 'public');
                }
            }

            $data['archivo_url'] = $path;
        }
        $documento = $this->documentoService->update($data, $documento->id);
        return response()
            ->json([
                'message' => MessageHttp::ACTUALIZADO_CORRECTAMENTE,
                'data' => $documento
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Documento $documento)
    {
        $documento = $this->documentoService->destroy($documento);
        return response()
            ->json([
                'message' => MessageHttp::ELIMINADO_CORRECTAMENTE,
                'data' => $documento
            ]);
    }
    public function listarDocNormasActivas($categoria)
    {
        $documentos = $this->documentoService->listarDocNormasActivas($categoria);
        return response()
            ->json([
                'message' => MessageHttp::OBTENIDOS_CORRECTAMENTE,
                'data' => $documentos
            ]);
    }
    public function listarDocTramitesActivas($categoria)
    {
        $documentos = $this->documentoService->listarDocTramitesActivas($categoria);
        return response()
            ->json([
                'message' => MessageHttp::OBTENIDOS_CORRECTAMENTE,
                'data' => $documentos
            ]);
    }
}
