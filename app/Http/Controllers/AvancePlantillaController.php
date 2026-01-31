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


    public function show(AvancePlantilla $avancePlantilla = null)
    {
        if ($avancePlantilla) {
            $data = [
                'message' => 'Materia obtenida correctamente',
                'data' => $avancePlantilla
            ];
        } else {

            $avancesPlantillas = $this->avancePlantillaService->listarActivos();
            $data = [
                'message' => 'Plantillas obtenidas correctamente',
                'data' => $avancesPlantillas
            ];
        }

        return response()->json($data);
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
