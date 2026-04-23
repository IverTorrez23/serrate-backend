<?php

namespace App\Http\Controllers;

use App\Enums\MessageHttp;
use App\Http\Requests\UpdateAcuerdosUsuariosRequest;
use App\Http\Requests\UpdateArancelAbogadoRequest;
use App\Models\TablaConfig;
use Illuminate\Http\Request;
use App\Services\TablaConfigService;
use App\Http\Requests\UpdateTablaConfigRequest;

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
        $data = $request->only([
            'titulo_index',
            'texto_index'
        ]);
        /*if ($request->hasFile('imagen_index')) {
            $file = $request->file('imagen_index');
            $pathindex = $file->store('uploads/img/config', 'public');
            $data['imagen_index'] = $pathindex;
        }*/
        if ($request->hasFile('imagen_index')) {
            $file = $request->file('imagen_index');

            $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $destinationPath = '/home/sites/htyg9449/public_html/api.teleprocuraduria.lex.net.bo/uploads/img/config';
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $file->move($destinationPath, $filename);
            $data['imagen_index'] = 'uploads/img/config/' . $filename;
        }

        if ($request->hasFile('imagen_index_mobil')) {
            $file = $request->file('imagen_index_mobil');
            //$pathindexMobil = $file->store('uploads/img/config', 'public');
            //$data['imagen_index_mobil'] = $pathindexMobil;
            // Nombre único para evitar sobrescribir archivos
            $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $destinationPath = '/home/sites/htyg9449/public_html/api.teleprocuraduria.lex.net.bo/uploads/img/config';
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $file->move($destinationPath, $filename);
            $data['imagen_index_mobil'] = 'uploads/img/config/' . $filename;
        }
        if ($request->hasFile('imagen_logo')) {
            $file = $request->file('imagen_logo');
            //$pathlogo = $file->store('uploads/img/config', 'public');
            //$data['imagen_logo'] = $pathlogo;
            // Nombre único para evitar sobrescribir archivos
            $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $destinationPath = '/home/sites/htyg9449/public_html/api.teleprocuraduria.lex.net.bo/uploads/img/config';
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $file->move($destinationPath, $filename);
            $data['imagen_logo'] = 'uploads/img/config/' . $filename;
        }
        $tablaConfig = $this->tablaConfigService->update($data, 1);
        return response()->json([
            'message' => MessageHttp::ACTUALIZADO_CORRECTAMENTE,
            'data' => $tablaConfig
        ]);
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
        if ($request->hasFile('archivo_url')) {
            $file = $request->file('archivo_url');
            //$pathArancel = $file->store('uploads/img/aranceles', 'public');
            //$data['archivo_url'] = $pathArancel;
            $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $destinationPath = '/home/sites/htyg9449/public_html/api.teleprocuraduria.lex.net.bo/uploads/img/aranceles';
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $file->move($destinationPath, $filename);
            $data['archivo_url'] = 'uploads/img/aranceles/' . $filename;
        }
        $data['nombre'] = $request->nombre;
        $tablaConfig = $this->tablaConfigService->update($data, 1);
        return response()->json([
            'message' => MessageHttp::ACTUALIZADO_CORRECTAMENTE,
            'data' => $tablaConfig
        ]);
    }
    public function obtenerArancelAbogados()
    {
        $datosAranceles = $this->tablaConfigService->obtenerArancelAbogados();
        return response()->json([
            'message' => MessageHttp::ACTUALIZADO_CORRECTAMENTE,
            'data' => $datosAranceles
        ]);
    }
    public function updataAcuerdosUsuarios(UpdateAcuerdosUsuariosRequest $request)
    {
        if ($request->hasFile('url_acuerdo_lider')) {
            $file = $request->file('url_acuerdo_lider');
            //$pathArancel = $file->store('uploads/img/acuerdos', 'public');
            //$data['url_acuerdo_lider'] = $pathArancel;
            $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $destinationPath = '/home/sites/htyg9449/public_html/api.teleprocuraduria.lex.net.bo/uploads/img/acuerdos';
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $file->move($destinationPath, $filename);
            $data['url_acuerdo_lider'] = 'uploads/img/acuerdos/' . $filename;
        }
        if ($request->hasFile('url_acuerdo_indep')) {
            $file = $request->file('url_acuerdo_indep');
            //$pathArancel = $file->store('uploads/img/acuerdos', 'public');
            //$data['url_acuerdo_indep'] = $pathArancel;
            $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $destinationPath = '/home/sites/htyg9449/public_html/api.teleprocuraduria.lex.net.bo/uploads/img/acuerdos';
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $file->move($destinationPath, $filename);
            $data['url_acuerdo_indep'] = 'uploads/img/acuerdos/' . $filename;
        }
        if ($request->hasFile('url_acuerdo_proc')) {
            $file = $request->file('url_acuerdo_proc');
            //$pathArancel = $file->store('uploads/img/acuerdos', 'public');
            //$data['url_acuerdo_proc'] = $pathArancel;
            $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $destinationPath = '/home/sites/htyg9449/public_html/api.teleprocuraduria.lex.net.bo/uploads/img/acuerdos';
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $file->move($destinationPath, $filename);
            $data['url_acuerdo_proc'] = 'uploads/img/acuerdos/' . $filename;
        }
        $tablaConfig = $this->tablaConfigService->update($data, 1);
        return response()->json([
            'message' => MessageHttp::ACTUALIZADO_CORRECTAMENTE,
            'data' => $tablaConfig
        ]);
    }
}
