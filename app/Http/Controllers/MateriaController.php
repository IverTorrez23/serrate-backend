<?php

namespace App\Http\Controllers;

use App\Models\Materia;
use App\Constants\Estado;
use App\Enums\MessageHttp;
use Illuminate\Http\Request;
use App\Http\Resources\MateriaCollection;
use App\Http\Requests\StoreMateriaRequest;
use App\Http\Requests\UpdateMateriaRequest;
use App\Services\MateriaService;

class MateriaController extends Controller
{
    protected $materiaService;

    public function __construct(MateriaService $materiaService)
    {
        $this->materiaService = $materiaService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Materia::active();

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
        $materias = $query->paginate($perPage);

        return new MateriaCollection($materias);
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
    public function store(StoreMateriaRequest $request)
    {
        $estado = Estado::ACTIVO;
        $materia = Materia::create([
            'nombre' => $request->nombre,
            'abreviatura' => $request->abreviatura,
            'estado' => $estado,
            'es_eliminado' => 0
        ]);

        $data = [
            'message' => MessageHttp::CREADO_CORRECTAMENTE,
            'data' => $materia
        ];
        return response()
            ->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(Materia $materia)
    {
        try {
            $data = [
                'message' => 'Materia obtenida correctamente',
                'data' => $materia
            ];
            return response()->json($data);
        } catch (\Throwable $e) {

            return response()->json([
                'status'  => 'error',
                'message' => 'Error al obtener materias.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
                'line'    => config('app.debug') ? $e->getLine() : null,
                'file'    => config('app.debug') ? $e->getFile() : null,
            ], 500);
        }
    }
    public function listarActivos()
    {
        try {
            $materias = $this->materiaService->listarActivos();
            return response()->json([
                'message' => 'Materias obtenidas correctamente',
                'data'    => $materias
            ], 200);
        } catch (\Throwable $e) {

            return response()->json([
                'status'  => 'error',
                'message' => 'Error al obtener materias.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
                'line'    => config('app.debug') ? $e->getLine() : null,
                'file'    => config('app.debug') ? $e->getFile() : null,
            ], 500);
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Materia $materia)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMateriaRequest $request, Materia $materia)
    {
        // $materia->nombre        =$request->nombre;
        // $materia->abreviatura   =$request->abreviatura;
        // $materia->save();
        $materia->update($request->only([
            'nombre',
            'abreviatura',
            'estado',
            'es_eliminado'
        ]));
        $data = [
            'message' => MessageHttp::ACTUALIZADO_CORRECTAMENTE,
            'data' => $materia
        ];
        return response()->json($data);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Materia $materia)
    {
        $materia->es_eliminado   = 1;
        $materia->save();
        $data = [
            'message' => MessageHttp::ELIMINADO_CORRECTAMENTE,
            'data' => $materia
        ];
        return response()->json($data);
    }
}
