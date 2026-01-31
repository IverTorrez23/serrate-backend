<?php
namespace App\Services;

use App\Constants\Estado;
use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaService
{
    public function store($data)
    {
        $categoria = Categoria::create([
            'nombre' => $data['nombre'],
            'abreviatura' => $data['abreviatura'],
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0
        ]);
        return $categoria;
    }
    public function update($data, $categoriaId)
    {
        $categoria = Categoria::findOrFail($categoriaId);
        $categoria->update($data);
        return $categoria;
    }
    public function obtenerUno($categoriaId)
    {
        $categoria = Categoria::findOrFail($categoriaId);
        return $categoria;
    }
    public function listarActivos()
    {
        $categorias = Categoria::where('estado', Estado::ACTIVO)
                     ->where('es_eliminado', 0)
                     ->get();
      return $categorias;
    }

}
