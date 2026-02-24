<?php

namespace App\Http\Controllers;

use App\Constants\Estado;
use App\Enums\MessageHttp;
use Illuminate\Http\Request;
use App\Models\AvancePlantilla;
use App\Http\Resources\AvancePlantillaCollection;
use App\Http\Requests\StoreAvancePlantillaRequest;
use App\Http\Requests\UpdateAvancePlantillaRequest;
use App\Services\AvancePlantillaService;
use Illuminate\Support\Facades\Log;

class AvancePlantillaController extends Controller
{

    protected $avancePlantillaService;

    public function __construct(AvancePlantillaService $avancePlantillaService)
    {
        $this->avancePlantillaService = $avancePlantillaService;
    }

    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $query = AvancePlantilla::active();

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
        $avancePlantilla = $query->paginate($perPage);

        return new AvancePlantillaCollection($avancePlantilla);
    }


    public function store(StoreAvancePlantillaRequest $request)
    {
        $avancePlantilla = AvancePlantilla::create([
            'nombre' => $request->nombre,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0
        ]);
        $data = [
            'message' => MessageHttp::CREADO_CORRECTAMENTE,
            'data' => $avancePlantilla
        ];
        return response()
            ->json($data);
    }


    public function show(AvancePlantilla $avancePlantilla)
    {
        try {
            $data = [
                'message' => 'Plantilla obtenidas correctamente',
                'data' => $avancePlantilla
            ];
            return response()->json($data);
        } catch (\Throwable $e) {

            return response()->json([
                'status'  => 'error',
                'message' => 'Error al obtener Plantilla.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
                'line'    => config('app.debug') ? $e->getLine() : null,
                'file'    => config('app.debug') ? $e->getFile() : null,
            ], 500);
        }
    }
    public function listarActivos()
    {
        try {
            $avancesPlantillas = $this->avancePlantillaService->listarActivos();
            return response()->json([
                'message' => 'Plantillas obtenidas correctamente',
                'data'    => $avancesPlantillas
            ], 200);
        } catch (\Throwable $e) {

            return response()->json([
                'status'  => 'error',
                'message' => 'Error al obtener plantillas.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
                'line'    => config('app.debug') ? $e->getLine() : null,
                'file'    => config('app.debug') ? $e->getFile() : null,
            ], 500);
        }
    }

    public function listarPlantillaPorId($idPlantilla)
    {
        $plantilla = $this->avancePlantillaService->listarPlantillaPorId($idPlantilla);
        return new AvancePlantillaCollection($plantilla);
    }
    public function update(UpdateAvancePlantillaRequest $request, AvancePlantilla $avancePlantilla)
    {
        $avancePlantilla->update($request->only([
            'nombre',
            'estado',
            'es_eliminado'
        ]));
        $data = [
            'message' => MessageHttp::ACTUALIZADO_CORRECTAMENTE,
            'data' => $avancePlantilla
        ];
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AvancePlantilla $avancePlantilla)
    {
        $avancePlantilla->es_eliminado   = 1;
        $avancePlantilla->save();
        $data = [
            'message' => MessageHttp::ELIMINADO_CORRECTAMENTE,
            'data' => $avancePlantilla
        ];
        return response()->json($data);
    }
}
