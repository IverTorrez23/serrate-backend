<?php

namespace App\Http\Controllers;

use Exception;
use App\Http\Resources\MateriaCollection;
use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Enums\MessageHttp;
use App\Http\Requests\StoreNotificacionRequest;
use App\Http\Requests\UpdateNotificacionRequest;
use App\Services\NotificacionService;
use Illuminate\Support\Facades\Auth;

class NotificacionController extends Controller
{
    protected $notificacionService;
    public function __construct(NotificacionService $notificacionService)
    {
        $this->notificacionService = $notificacionService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Notificacion::active();

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
        $notificaciones = $query->paginate($perPage);

        return new MateriaCollection($notificaciones);
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
    public function store(StoreNotificacionRequest $request)
    {
        DB::beginTransaction();
        try {
            $idUser = Auth::user()->id;
            $data = [
                'tipo' => $request->tipo,
                'evento' => $request->evento,
                'emisor' => $request->emisor,
                'nombre_emisor' => $request->nombre_emisor,
                'tipo_receptor' => $request->tipo_receptor,
                'receptor_estatico' => $request->receptor_estatico,
                'descripcion_receptor_estatico' => $request->descripcion_receptor_estatico,
                'envia_notificacion' => $request->envia_notificacion,
                'asunto' => $request->asunto,
                'texto' => $request->texto,
                'usuario_id' => $idUser
            ];
            $notificacion = $this->notificacionService->store($data);

            DB::commit();
            return response()->json([
                'message' => MessageHttp::CREADO_CORRECTAMENTE,
                'data' => $notificacion
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error registrar notificacion: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error interno al registrar notificacion',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Notificacion $notificacion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notificacion $notificacion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNotificacionRequest $request, Notificacion $notificacion)
    {
        DB::beginTransaction();
        try {
            $data = $request->only([
                'tipo',
                'evento',
                'emisor',
                'nombre_emisor',
                'tipo_receptor',
                'receptor_estatico',
                'descripcion_receptor_estatico',
                'asunto',
                'envia_notificacion',
                'texto'
            ]);

            $notificacion = $this->notificacionService->update($data, $notificacion->id);

            DB::commit();
            return response()->json([
                'message' => MessageHttp::ACTUALIZADO_CORRECTAMENTE,
                'data' => $notificacion
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error actualizar notificacion: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error interno al actualizar notificacion',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notificacion $notificacion)
    {
        DB::beginTransaction();
        try {
            $notificacion = $this->notificacionService->destroy($notificacion);

            DB::commit();
            return response()->json([
                'message' => MessageHttp::ELIMINADO_CORRECTAMENTE,
                'data' => $notificacion
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error eliminar notificacion: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error interno al eliminar notificacion',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
