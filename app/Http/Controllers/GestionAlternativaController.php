<?php

namespace App\Http\Controllers;

use Exception;
use App\Constants\FechaHelper;
use App\Constants\TipoUsuario;
use Carbon\Carbon;
use App\Enums\MessageHttp;
use Illuminate\Http\Request;
use App\Models\GestionAlternativa;
use App\Services\GestionAlternativaService;
use App\Http\Requests\StoreGestionAlternativaRequest;
use App\Http\Requests\UpdateGestionAlternativaRequest;
use App\Services\OrdenService;
use App\Services\ProcuraduriaDescargaService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class GestionAlternativaController extends Controller
{
    protected $gestionAlternativaService;
    protected $ordenService;
    protected $procuraduriaDescargaService;

    public function __construct(
        GestionAlternativaService $gestionAlternativaService,
        OrdenService $ordenService,
        ProcuraduriaDescargaService $procuraduriaDescargaService
    ) {
        $this->gestionAlternativaService = $gestionAlternativaService;
        $this->ordenService = $ordenService;
        $this->procuraduriaDescargaService = $procuraduriaDescargaService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StoreGestionAlternativaRequest $request)
    {

        $fechaHora = FechaHelper::fechaHoraBolivia();
        $descarga = $this->procuraduriaDescargaService->obtenerUnoPorOrdenId($request->orden_id);
        if ($descarga) {
            return response()->json([
                'message' => 'No se puede registrar Gestión porque ya se hizo la descarga',
                'data' => null
            ], 409);
        }
        if ($this->gestionAlternativaService->hayGestionesAbiertasDeOrdenes($request->orden_id)) {
            return response()->json([
                'message' => 'No se puede registrar Gestión porque aún no se ha cerrado la anterior.',
                'data' => null
            ], 409);
        }

        DB::beginTransaction();
        try {
            $data = [
                'solicitud_gestion' => $request->solicitud_gestion,
                'fecha_solicitud' => $fechaHora,
                'tribunal_id' => $request->tribunal_id,
                'cuerpo_expediente_id' => $request->cuerpo_expediente_id,
                'detalle_gestion' => '',
                'fecha_respuesta' => null,
                'orden_id' => $request->orden_id,
            ];
            $gestionAlternativa = $this->gestionAlternativaService->store($data);
            DB::commit();
            return response()->json([
                'message' => MessageHttp::CREADO_CORRECTAMENTE,
                'data' => $gestionAlternativa
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error registrar retiro: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error registrar retiro',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(GestionAlternativa $gestionAlternativa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GestionAlternativa $gestionAlternativa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGestionAlternativaRequest $request, GestionAlternativa $gestionAlternativa)
    {
        $tipoUsuario = Auth::user()->tipo;
        $fechaHora = FechaHelper::fechaHoraBolivia();
        if ($gestionAlternativa->detalle_gestion != '' && $tipoUsuario === TipoUsuario::PROCURADOR) {
            return response()->json([
                'message' => 'No puede hacer el registro porque el abogado ya sugirio la gestión',
                'data' => null
            ], 409);
        }
        $descarga = $this->procuraduriaDescargaService->obtenerUnoPorOrdenId($gestionAlternativa->orden_id);
        if ($descarga) {
            return response()->json([
                'message' => 'No se puede registrar Gestión porque ya se hizo la descarga de la orden',
                'data' => null
            ], 409);
        }
        $cantidad = $this->gestionAlternativaService->contarGestionesPosteriores($gestionAlternativa->id, $gestionAlternativa->orden_id);
        if ($cantidad > 0) {
            return response()->json([
                'message' => 'No se puede completar el registro porque ya hay una gestion posterior',
                'data' => null
            ], 409);
        }

        DB::beginTransaction();
        try {

            $data = $request->only([
                'detalle_gestion',
                'solicitud_gestion',
                'tribunal_id',
                'cuerpo_expediente_id'
            ]);
            if ($data['detalle_gestion']) {
                $data['fecha_respuesta'] = $fechaHora;
            }
            $gestionAlternativa = $this->gestionAlternativaService->update($data, $gestionAlternativa->id);

            DB::commit();
            $data = [
                'message' => MessageHttp::ACTUALIZADO_CORRECTAMENTE,
                'data' => $gestionAlternativa
            ];
            return response()->json($data);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar gestion alternativa: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error al actualizar gestion alternativa',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GestionAlternativa $gestionAlternativa)
    {
        if ($gestionAlternativa->detalle_gestion != '') {
            return response()->json([
                'message' => 'No puede anular la solicitud porque el abogado ya sugirió la gestión',
                'data' => null
            ], 409);
        }
        DB::beginTransaction();
        try {
            $gestionAlternativa = $this->gestionAlternativaService->destroy($gestionAlternativa);
            DB::commit();
            $data = [
                'message' => MessageHttp::ELIMINADO_CORRECTAMENTE,
                'data' => $gestionAlternativa
            ];
            return response()->json($data);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar registro: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error al eliminar registro registro',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function obtenerPorOrdenId($ordenId)
    {
        $gestionAlternativas = $this->gestionAlternativaService->obtenerPorOrdenId($ordenId);
        $data = [
            'message' => MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data' => $gestionAlternativas
        ];
        return response()->json($data);
    }
    public function obtenerUnoById($gestionId)
    {
        $gestionAlternativas = $this->gestionAlternativaService->obtenerUnoById($gestionId);
        $data = [
            'message' => MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data' => $gestionAlternativas
        ];
        return response()->json($data);
    }
    public function contarGestionesPosteriores($gestionId, $ordenId)
    {
        $cantidad = $this->gestionAlternativaService->contarGestionesPosteriores($gestionId, $ordenId);
        $data = [
            'message' => MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data' => $cantidad
        ];
        return response()->json($data);
    }
}
