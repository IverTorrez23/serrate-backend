<?php
namespace App\Services;

use App\Constants\Estado;
use App\Models\Distrito;
use Illuminate\Http\Request;

class DistritoService
{
    public function store($data)
    {
        $distrito = Distrito::create([
            'nombre' => $data['nombre'],
            'abreviatura' => $data['abreviatura'],
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0
        ]);
        return $distrito;
    }
    public function update($data, $distritoId)
    {
        $distrito = Distrito::findOrFail($distritoId);
        $distrito->update($data);
        return $distrito;
    }
    public function obtenerUno($distritoId)
    {
        $distrito = Distrito::findOrFail($distritoId);
        return $distrito;
    }
    public function listarActivos()
    {
        $distritos = Distrito::where('estado', Estado::ACTIVO)
                     ->where('es_eliminado', 0)
                     ->get();
      return $distritos;
    }

}
