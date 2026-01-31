<?php

namespace App\Http\Controllers;

use App\Models\Distrito;
use App\Constants\Estado;
use App\Enums\MessageHttp;
use Illuminate\Http\Request;
use App\Http\Resources\DistritoCollection;
use App\Http\Requests\StoreDistritoRequest;
use App\Http\Requests\UpdateDistritoRequest;
use App\Services\DistritoService;

class DistritoController extends Controller
{
    protected $distritoService;

    public function __construct(DistritoService $distritoService)
    {
        $this->distritoService = $distritoService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Distrito::active();

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
        $distritos = $query->paginate($perPage);

        return new DistritoCollection($distritos);
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
    public function store(StoreDistritoRequest $request)
    {
        $estado=Estado::ACTIVO;
        $distrito=Distrito::create([
            'nombre'=>$request->nombre,
            'abreviatura'=>$request->abreviatura,
            'estado'=>$estado,
            'es_eliminado'=>0
         ]);
         $data=[
            'message'=> MessageHttp::CREADO_CORRECTAMENTE,
            'data'=>$distrito
         ];
         return response()
               ->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(Distrito $distrito = null)
    {
        if ($distrito) {
            $data = [
                'message' => 'Distrito obtenido correctamente',
                'data' => $distrito
            ];
        } else {
            $distritos = $this->distritoService->listarActivos();
            $data = [
                'message' => 'Distritos obtenidos correctamente',
                'data' => $distritos
            ];
        }

        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Distrito $distrito)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDistritoRequest $request, Distrito $distrito)
    {
        $distrito->update($request->only([
            'nombre',
            'abreviatura',
            'estado',
            'es_eliminado']));
        $data=[
        'message'=> MessageHttp::ACTUALIZADO_CORRECTAMENTE,
        'data'=>$distrito
        ];
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Distrito $distrito)
    {
        $distrito->es_eliminado   =1;
         $distrito->save();
         $data=[
            'message'=> MessageHttp::ELIMINADO_CORRECTAMENTE,
            'data'=>$distrito
        ];
        return response()->json($data);
    }
}
