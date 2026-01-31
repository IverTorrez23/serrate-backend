<?php
namespace App\Services;

use App\Constants\Estado;
use App\Models\Materia;
use Illuminate\Http\Request;

class MateriaService
{
    public function store($data)
    {
        $materia = Materia::create([
            'nombre' => $data['nombre'],
            'abreviatura' => $data['abreviatura'],
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0
        ]);
        return $materia;
    }
    public function update($data, $materiaId)
    {
        $materia = Materia::findOrFail($materiaId);
        $materia->update($data);
        return $materia;
    }
    public function obtenerUno($materiaId)
    {
        $materia = Materia::findOrFail($materiaId);
        return $materia;
    }
    public function listarActivos()
    {
        $materias = Materia::where('estado', Estado::ACTIVO)
                     ->where('es_eliminado', 0)
                     ->get();
      return $materias;
    }

}
