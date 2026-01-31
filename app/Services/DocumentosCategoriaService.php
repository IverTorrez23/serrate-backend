<?php

namespace App\Services;

use App\Constants\Estado;
use App\Constants\TipoDocumento;
use App\Models\DocumentosCategoria;
use Illuminate\Http\Request;

class DocumentosCategoriaService
{
    public function store($data)
    {
        $documentoCategoria = DocumentosCategoria::create([
            'nombre' => $data['nombre'],
            'tipo' => $data['tipo'],
            'categoria_id' => $data['categoria_id'],
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0
        ]);
        return $documentoCategoria;
    }
    public function update($data, $docCategoriaId)
    {
        $documentoCategoria = DocumentosCategoria::findOrFail($docCategoriaId);
        $documentoCategoria->update($data);
        return $documentoCategoria;
    }
    public function obtenerUno($docCategoriaId)
    {
        $documentoCategoria = DocumentosCategoria::findOrFail($docCategoriaId);
        return $documentoCategoria;
    }
    public function listarActivos()
    {
        $documentoCategoria = DocumentosCategoria::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->get();
        return $documentoCategoria;
    }
    public function listarSubcategorias($categoriaID)
    {
        $documentoCategoria = DocumentosCategoria::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('categoria_id', $categoriaID)
            ->orderBy('nombre', 'ASC')
            ->get();
        return $documentoCategoria;
    }
    public function listarCategorias()
    {
        $documentoCategoria = DocumentosCategoria::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('categoria_id', 0)
            ->orderBy('nombre', 'ASC')
            ->get();
        return $documentoCategoria;
    }
    public function listarCategoriasTramites()
    {
        $documentoCategoria = DocumentosCategoria::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('categoria_id', 0)
            ->where('tipo', TipoDocumento::TRAMITES)
            ->orderBy('nombre', 'ASC')
            ->get();
        return $documentoCategoria;
    }
    public function listarCategoriasNormas()
    {
        $documentoCategoria = DocumentosCategoria::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('categoria_id', 0)
            ->where('tipo', TipoDocumento::NORMAS)
            ->orderBy('nombre', 'ASC')
            ->get();
        return $documentoCategoria;
    }

}
