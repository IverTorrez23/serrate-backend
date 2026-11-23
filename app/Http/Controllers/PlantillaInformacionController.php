<?php

namespace App\Http\Controllers;

use Exception;
use App\Enums\MessageHttp;
use App\Http\Requests\StorePlantillaInformacionRequest;
use App\Http\Requests\UpdatePlantillaInformacionRequest;
use App\Http\Resources\PisoCollection;
use App\Models\PlantillaInformacion;
use App\Services\PlantillaInformacionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PlantillaInformacionController extends Controller
{
    protected $plantillaInformacionService;

    public function __construct(PlantillaInformacionService $plantillaInformacionService)
    {
        $this->plantillaInformacionService = $plantillaInformacionService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PlantillaInformacion::active();

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
        $plantillas = $query->paginate($perPage);

        return new PisoCollection($plantillas);
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
    public function store(StorePlantillaInformacionRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = [
                'nombre' => $request->nombre,
                'cuerpo_plantilla' => $request->cuerpo_plantilla
            ];
            $plantilla = $this->plantillaInformacionService->store($data);
            DB::commit();
            return response()->json([
                'message' => MessageHttp::CREADO_CORRECTAMENTE,
                'data' => $plantilla
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error registrar plantilla: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error registrar plantilla',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PlantillaInformacion $plantillaInformacion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PlantillaInformacion $plantillaInformacion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePlantillaInformacionRequest $request, PlantillaInformacion $plantillaInformacion)
    {
        DB::beginTransaction();
        try {
            $data = $request->only([
                'nombre',
                'cuerpo_plantilla'
            ]);

            $plantilla = $this->plantillaInformacionService->update($data, $plantillaInformacion->id);
            DB::commit();
            return response()->json([
                'message' => MessageHttp::ACTUALIZADO_CORRECTAMENTE,
                'data' => $plantilla
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error registrar plantilla: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error registrar plantilla',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PlantillaInformacion $plantillaInformacion)
    {
        $plantilla = $this->plantillaInformacionService->destroy($plantillaInformacion);

        return response()->json([
            'message' => MessageHttp::ELIMINADO_CORRECTAMENTE,
            'data' => $plantilla
        ], 200);
    }

    public function listarActivos()
    {
        try {
            $plantillas = $this->plantillaInformacionService->listarActivos();
            return response()->json([
                'message' => 'plantillas de informacion obtenidos correctamente',
                'data'    => $plantillas
            ], 200);
        } catch (\Throwable $e) {

            return response()->json([
                'status'  => 'error',
                'message' => 'Error al obtener plantillas de informacion.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
                'line'    => config('app.debug') ? $e->getLine() : null,
                'file'    => config('app.debug') ? $e->getFile() : null,
            ], 500);
        }
    }
}
