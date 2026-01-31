<?php

namespace App\Http\Controllers;

use App\Models\Posta;
use App\Enums\MessageHttp;
use Illuminate\Http\Request;
use App\Http\Resources\PostaCollection;
use App\Http\Requests\StorePostaRequest;
use App\Http\Requests\UpdatePostaRequest;
use App\Services\PostaService;

class PostaController extends Controller
{
    protected $postaService;
    public function __construct(PostaService $postaService)
    {
        $this->postaService = $postaService;
    }
    public function index(Request $request)
    {
        $postas = $this->postaService->index($request);
        return new PostaCollection($postas);
    }


    public function store(StorePostaRequest $request)
    {
        $posta = $this->postaService->store($request->all());
        return response()->json([
            'message' => MessageHttp::CREADO_CORRECTAMENTE,
            'data' => $posta
        ]);
    }



    public function show(Posta $posta = null)
    {
        if ($posta) {
            $data = [
                'message' => 'Posta obtenida correctamente',
                'data' => $posta
            ];
        } else {

            $postas = $this->postaService->show();
            $data = [
                'message' => 'Postas obtenidas correctamente',
                'data' => $postas
            ];
        }

        return response()->json($data);
    }

    public function listarPorIdPlantilla(Request $request, $idPlantilla = null)
    {
        $postas = $this->postaService->listarPorIdPlantilla($request, $idPlantilla);
        return new PostaCollection($postas);
    }

    public function update(UpdatePostaRequest $request, Posta $posta)
    {
        $updatedPosta = $this->postaService->update($request->all(), $posta->id);
        return response()->json([
            'message' => MessageHttp::ACTUALIZADO_CORRECTAMENTE,
            'data' => $updatedPosta
        ]);
    }


    public function destroy(Posta $posta)
    {
        $deletedPosta = $this->postaService->destroy($posta->id);
        return response()->json([
            'message' => MessageHttp::ELIMINADO_CORRECTAMENTE,
            'data' => $deletedPosta
        ]);
    }
}
