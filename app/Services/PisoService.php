<?php
namespace App\Services;

use App\Constants\Estado;
use Illuminate\Http\Request;
use App\Models\Piso;

class PisoService
{
    public function store($data)
    {
        $piso = Piso::create([
            'nombre' => $data['nombre'],
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0
        ]);
        return $piso;
    }
    public function update($data, $pisoId)
    {
        $piso = Piso::findOrFail($pisoId);
        $piso->update($data);
        return $piso;
    }
    public function obtenerUno($pisoId)
    {
        $piso = Piso::findOrFail($pisoId);
        return $piso;
    }
    public function listarActivos()
    {
        $piso = Piso::where('estado', Estado::ACTIVO)
                     ->where('es_eliminado', 0)
                     ->get();
      return $piso;
    }

}
