<?php

namespace App\Services;

use App\Constants\Estado;
use App\Models\Documento;
use Illuminate\Http\Request;
use App\Constants\TipoDocumento;

class DocumentoService
{
    public function store($data)
    {
        $documento = Documento::create([
            'nombre' => $data['nombre'],
            'archivo_url' => $data['archivo_url'],
            'tipo' => $data['tipo'],
            'categoria_id' => $data['categoria_id'],
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0
        ]);
        return $documento;
    }
    public function update($data, $documentoId)
    {
        $documento = Documento::findOrFail($documentoId);
        $documento->update($data);
        return $documento;
    }
    public function destroy(Documento $documento)
    {
        $documento->es_eliminado = 1;
        $documento->save();
        return $documento;
    }
    public function listarDocNormasActivas($categoria)
    {
        $docNormas = Documento::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('categoria_id', $categoria)
            ->where('tipo', TipoDocumento::NORMAS)
            ->orderBy('nombre', 'ASC')
            ->get();
        return $docNormas;
    }
    public function listarDocTramitesActivas($categoria)
    {
        $docNormas = Documento::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('categoria_id', $categoria)
            ->where('tipo', TipoDocumento::TRAMITES)
            ->orderBy('nombre', 'ASC')
            ->get();
        return $docNormas;
    }
}
