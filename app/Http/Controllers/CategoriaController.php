<?php

namespace App\Http\Controllers;

use App\Constants\Estado;
use App\Models\Categoria;
use App\Enums\MessageHttp;
use Illuminate\Http\Request;
use App\Http\Resources\CategoriaCollection;
use App\Http\Requests\StoreCategoriaRequest;
use App\Http\Requests\UpdateCategoriaRequest;
use App\Services\CategoriaService;

class CategoriaController extends Controller
{
    protected $categoriaService;

    public function __construct(CategoriaService $categoriaService)
    {
        $this->categoriaService = $categoriaService;
    }
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $query = Categoria::active();

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
        $categorias = $query->paginate($perPage);

        return new CategoriaCollection($categorias);
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
    public function store(StoreCategoriaRequest $request)
    {
        $estado=Estado::ACTIVO;
        $categoria=Categoria::create([
            'nombre'=>$request->nombre,
            'abreviatura'=>$request->abreviatura,
            'estado'=>$estado,
            'es_eliminado'=>0
         ]);
         $data=[
            'message'=> MessageHttp::CREADO_CORRECTAMENTE,
            'data'=>$categoria
         ];
         return response()
               ->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(Categoria $categoria = null)
    {
        if ($categoria) {

            $data = [
                'message' => 'Categoria obtenida correctamente',
                'data' => $categoria
            ];
        } else {

            $categorias = $this->categoriaService->listarActivos();
            $data = [
                'message' => 'Categorias obtenidas correctamente',
                'data' => $categorias
            ];
        }

        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Categoria $categoria)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoriaRequest $request, Categoria $categoria)
    {
        $categoria->update($request->only([
            'nombre',
            'abreviatura',
            'estado',
            'es_eliminado']));
        $data=[
        'message'=> MessageHttp::ACTUALIZADO_CORRECTAMENTE,
        'data'=>$categoria
        ];
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Categoria $categoria)
    {
        $categoria->es_eliminado   =1;
         $categoria->save();
         $data=[
            'message'=> MessageHttp::ELIMINADO_CORRECTAMENTE,
            'data'=>$categoria
        ];
        return response()->json($data);
    }
}
