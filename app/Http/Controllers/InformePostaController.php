<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Constants\Estado;
use App\Enums\MessageHttp;
use App\Models\InformePosta;
use Illuminate\Http\Request;
use App\Http\Resources\InformePostaCollection;
use App\Http\Requests\StoreInformePostaRequest;
use App\Http\Requests\UpdateInformePostaRequest;
use App\Services\CausaPostaService;
use App\Services\InformePostaService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InformePostaController extends Controller
{
    protected $causaPostaService;
    protected $informePostaService;
    public function __construct(CausaPostaService $causaPostaService, InformePostaService $informePostaService)
    {
        $this->causaPostaService = $causaPostaService;
        $this->informePostaService = $informePostaService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $informePosta = InformePosta::where('es_eliminado', 0)
            ->where('estado', Estado::ACTIVO)
            ->paginate();
        return new InformePostaCollection($informePosta);
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
    public function store(StoreInformePostaRequest $request)
    {
        DB::beginTransaction();
        try {

            $informePosta = InformePosta::create([
                'foja_informe' => $request->foja_informe,
                'fecha_informe' => $request->fecha_informe,
                'calculo_gasto' => $request->calculo_gasto,
                'honorario_informe' => $request->honorario_informe,

                'fecha_truncamiento' => null,
                'esta_escrito' => 1,

                'foja_truncamiento' => $request->foja_truncamiento,
                'honorario_informe_truncamiento' => $request->honorario_informe_truncamiento,
                'tipoposta_id' => $request->tipoposta_id,
                'causaposta_id' => $request->causaposta_id,
                'estado' => Estado::ACTIVO,
                'es_eliminado' => 0
            ]);

            $dataCausaPosta = [
                'tiene_informe' => 1
            ];
            $causaPosta = $this->causaPostaService->update($dataCausaPosta, $request->causaposta_id);


            DB::commit();
            return response()->json([
                'message' => MessageHttp::CREADO_CORRECTAMENTE,
                'data' => $informePosta
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error registrar informe: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error registrar informe',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(InformePosta $informePosta)
    {
        $data = [
            'message' => MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data' => $informePosta
        ];
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InformePosta $informePosta)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInformePostaRequest $request, InformePosta $informePosta)
    {
        DB::beginTransaction();
        try {
            if ($request->has('tipoposta_id') && $request->tipoposta_id > 0) {
                $dataUpdateInforme = [
                    'foja_truncamiento' => $request->foja_truncamiento,
                    'fecha_truncamiento' => $request->fecha_truncamiento,
                    'honorario_informe_truncamiento' => $request->honorario_informe_truncamiento,
                    'tipoposta_id' => $request->tipoposta_id
                ];
            } else {
                $dataUpdateInforme = [
                    'foja_informe' => $request->foja_informe,
                    'fecha_informe' => $request->fecha_informe,
                    'honorario_informe' => $request->honorario_informe
                ];
            }


            $informePosta =  $this->informePostaService->update($dataUpdateInforme, $informePosta->id);

            DB::commit();
            return response()->json([
                'message' => MessageHttp::ACTUALIZADO_CORRECTAMENTE,
                'data' => $informePosta
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error update informe posta: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error update informe posta',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InformePosta $informePosta)
    {
        DB::beginTransaction();
        try {

            $causaPostaData = [
                'tiene_informe' => 0
            ];
            $this->causaPostaService->update($causaPostaData, $informePosta->causaposta_id);
            $informePosta->delete();

            DB::commit();
            return response()->json([
                'message' => MessageHttp::ELIMINADO_CORRECTAMENTE,
                'data' => $informePosta
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error eliminar informe posta: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error eliminar informe posta',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteTruncamiento(UpdateInformePostaRequest $request, InformePosta $informePosta)
    {
        DB::beginTransaction();
        try {
                $deleteTruncamientoData = [
                    'foja_truncamiento' => null,
                    'fecha_truncamiento' => null,
                    'honorario_informe_truncamiento' => null,
                    'tipoposta_id' => 0
                ];


            $informePosta =  $this->informePostaService->update($deleteTruncamientoData, $informePosta->id);

            DB::commit();
            return response()->json([
                'message' => MessageHttp::ACTUALIZADO_CORRECTAMENTE,
                'data' => $informePosta
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error update informe posta: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error update informe posta',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
