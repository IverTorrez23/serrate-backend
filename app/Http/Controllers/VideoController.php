<?php

namespace App\Http\Controllers;

use App\Constants\TipoUsuario;
use App\Models\Video;
use App\Enums\MessageHttp;
use Illuminate\Http\Request;
use App\Services\VideoService;
use App\Http\Resources\VideoCollection;
use App\Http\Requests\StoreVideoRequest;
use App\Http\Requests\UpdateVideoRequest;
use Illuminate\Support\Facades\Auth;

class VideoController extends Controller
{
    protected $videoService;
    public function __construct(VideoService $videoService)
    {
        $this->videoService = $videoService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Video::active();
        $query->where('tipo', 'PUBLICO');
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
        $videos = $query->paginate($perPage);

        return new VideoCollection($videos);
    }
    public function indexProcuradores(Request $request)
    {
        $query = Video::active();
        $query->where('tipo', 'PROCURADOR');

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
        $videos = $query->paginate($perPage);

        return new VideoCollection($videos);
    }
    public function indexAbogados(Request $request)
    {
        $query = Video::active();
        $query->where('tipo', 'ABOGADO');

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
        $videos = $query->paginate($perPage);

        return new VideoCollection($videos);
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
    public function store(StoreVideoRequest $request)
    {
        $embedUrl = $this->videoService->transformarUrlYoutube($request->link);
        $data = ([
            'link' => $embedUrl,
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'tipo' => $request->tipo
        ]);
        $video = $this->videoService->store($data);
        return response()
            ->json([
                'message' => MessageHttp::CREADO_CORRECTAMENTE,
                'data' => $video
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Video $video)
    {
        $video = $this->videoService->obtenerUno($video->id);
        $data = [
            'message' => MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data' => $video
        ];
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Video $video)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVideoRequest $request, Video $video)
    {
        $embedUrl = $this->videoService->transformarUrlYoutube($request->link);
        $data = $request->only([
            'titulo',
            'descripcion',
            'tipo'
        ]);
        $data['link'] = $embedUrl;
        $video = $this->videoService->update($data, $video->id);
        return response()->json([
            'message' => MessageHttp::ACTUALIZADO_CORRECTAMENTE,
            'data' => $video
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Video $video)
    {
        $video = $this->videoService->destroy($video->id);
        return response()->json([
            'message' => MessageHttp::ELIMINADO_CORRECTAMENTE,
            'data' => $video
        ]);
    }
    public function listarActivos()
    {
        $videos = $this->videoService->listarActivosPublico();
        return response()
            ->json([
                'message' => MessageHttp::OBTENIDOS_CORRECTAMENTE,
                'data' => $videos
            ]);
    }
    public function listarActivosSegunUsuario()
    {
        $tipoUser = Auth::user()->tipo;
        $videos = '';
        if ($tipoUser === TipoUsuario::ABOGADO_DEPENDIENTE || $tipoUser === TipoUsuario::ABOGADO_LIDER || $tipoUser === TipoUsuario::ABOGADO_INDEPENDIENTE) {
            $videos = $this->videoService->listarActivosAbogado();
        }
        if ($tipoUser === TipoUsuario::PROCURADOR) {
            $videos = $this->videoService->listarActivosProcurador();
        }
        return response()
            ->json([
                'message' => MessageHttp::OBTENIDOS_CORRECTAMENTE,
                'data' => $videos
            ]);
    }
}
