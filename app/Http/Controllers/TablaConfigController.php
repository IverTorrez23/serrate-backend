<?php

namespace App\Http\Controllers;

use App\Constants\GlobalVar;
use App\Enums\MessageHttp;
use App\Http\Requests\UpdateAcuerdosUsuariosRequest;
use App\Http\Requests\UpdateArancelAbogadoRequest;
use App\Models\TablaConfig;
use Illuminate\Http\Request;
use App\Services\TablaConfigService;
use App\Http\Requests\UpdateTablaConfigRequest;
use Illuminate\Support\Facades\Log;

class TablaConfigController extends Controller
{
    protected $tablaConfigService;
    public function __construct(TablaConfigService $tablaConfigService)
    {
        $this->tablaConfigService = $tablaConfigService;
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $tablaConfig = $this->tablaConfigService->mostarDatosTablaConfig();
        return response()->json([
            'message' => MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data' => $tablaConfig
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TablaConfig $tablaConfig)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTablaConfigRequest $request)
    {
        try {
            /*$data = $request->only([
                'titulo_index',
                'texto_index'
            ]);*/
            $data = [];
            
            if ($request->hasFile('imagen_index')) {
                $file = $request->file('imagen_index');

                $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
                
                $relativePath = 'uploads/img/config';
                $destinationPath = GlobalVar::path($relativePath);
                // DEBUG
                //Log::info('Ruta destino:', ['path' => $destinationPath]);
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                $file->move($destinationPath, $filename);
                $data['imagen_index'] = $relativePath . '/' . $filename;
            }

            if ($request->hasFile('imagen_index_mobil')) {
                $file = $request->file('imagen_index_mobil');
                
                $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
                
                $relativePath = 'uploads/img/config';
                $destinationPath = GlobalVar::path($relativePath);
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                $file->move($destinationPath, $filename);
                $data['imagen_index_mobil'] = $relativePath . '/' . $filename;
            }
            if ($request->hasFile('imagen_logo')) {
                $file = $request->file('imagen_logo');
                
                $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
                //$destinationPath = '/home/sites/htyg9449/public_html/api.teleprocuraduria.lex.net.bo/uploads/img/config'; //Para cargas en Prod
                //$destinationPath = 'uploads/img/config'; //Para cargas en local
                $relativePath = 'uploads/img/config';
                $destinationPath = GlobalVar::path($relativePath);
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                $file->move($destinationPath, $filename);
                $data['imagen_logo'] = $relativePath . '/' . $filename;
            }
            $tablaConfig = $this->tablaConfigService->update($data, 1);
            return response()->json([
                'status' => 'success',
                'message' => MessageHttp::ACTUALIZADO_CORRECTAMENTE,
                'data' => $tablaConfig
            ] ,200);
        } catch (\Throwable $e) {
            Log::error('Error al actualizar imagenes index', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo actualizar imagenes index.',
                'error' => config('app.debug') ? $e->getMessage() : null,
                'line' => config('app.debug') ? $e->getLine() : null,
            ], 500);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TablaConfig $tablaConfig)
    {
        //
    }

    public function updataArancelesAbogado(UpdateArancelAbogadoRequest $request)
    {
        try {
            $data = [];

            if ($request->hasFile('archivo_url')) {
                $file = $request->file('archivo_url');

                $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());

                $relativePath = 'uploads/img/aranceles';
                $destinationPath = GlobalVar::path($relativePath);
                // DEBUG
                //Log::info('Ruta destino:', ['path' => $destinationPath]);

                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                $file->move($destinationPath, $filename);
                $data['archivo_url'] = $relativePath . '/' . $filename;
            }
            $data['nombre'] = $request->nombre;
            $tablaConfig = $this->tablaConfigService->update($data, 1);
            return response()->json([
                'message' => MessageHttp::ACTUALIZADO_CORRECTAMENTE,
                'data' => $tablaConfig
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al actualizar aranceles abogado', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo actualizar el arancel.',
                'error' => config('app.debug') ? $e->getMessage() : null,
                'line' => config('app.debug') ? $e->getLine() : null,
            ], 500);
        }
    }
    public function obtenerArancelAbogados()
    {
        $datosAranceles = $this->tablaConfigService->obtenerArancelAbogados();
        return response()->json([
            'message' => MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data' => $datosAranceles
        ]);
    }
    public function updataAcuerdosUsuarios(UpdateAcuerdosUsuariosRequest $request)
    {
        try {
            $data = [];

            if ($request->hasFile('url_acuerdo_lider')) {
                $file = $request->file('url_acuerdo_lider');

                $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());

                $relativePathLid = 'uploads/img/acuerdos';
                $destinationPath = GlobalVar::path($relativePathLid);
                // DEBUG
                //Log::info('Ruta destino:', ['path' => $destinationPath]);
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                $file->move($destinationPath, $filename);
                $data['url_acuerdo_lider'] = $relativePathLid . '/' . $filename;
            }
            if ($request->hasFile('url_acuerdo_indep')) {
                $file = $request->file('url_acuerdo_indep');

                $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());

                $relativePathInd = 'uploads/img/acuerdos';
                $destinationPath = GlobalVar::path($relativePathInd);
                // DEBUG
                //Log::info('Ruta destino:', ['path' => $destinationPath]);
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                $file->move($destinationPath, $filename);
                $data['url_acuerdo_indep'] = $relativePathInd . '/' . $filename;
            }
            if ($request->hasFile('url_acuerdo_proc')) {
                $file = $request->file('url_acuerdo_proc');

                $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());

                $relativePathProc = 'uploads/img/acuerdos';
                $destinationPath = GlobalVar::path($relativePathProc);
                // DEBUG
                //Log::info('Ruta destino:', ['path' => $destinationPath]);
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                $file->move($destinationPath, $filename);
                $data['url_acuerdo_proc'] = $relativePathProc . '/' . $filename;
            }
            $tablaConfig = $this->tablaConfigService->update($data, 1);
            return response()->json([
                'message' => MessageHttp::ACTUALIZADO_CORRECTAMENTE,
                'data' => $tablaConfig
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al actualizar acuerdos', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo actualizar acuerdo.',
                'error' => config('app.debug') ? $e->getMessage() : null,
                'line' => config('app.debug') ? $e->getLine() : null,
            ], 500);
        }
    }
}
