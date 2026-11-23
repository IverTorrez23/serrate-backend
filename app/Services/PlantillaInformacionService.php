<?php

namespace App\Services;

use App\Constants\Estado;
use App\Models\PlantillaInformacion;

class PlantillaInformacionService
{
    public function store($data)
    {
        $plantilla = PlantillaInformacion::create([
            'nombre' => $data['nombre'],
            'cuerpo_plantilla' => $data['cuerpo_plantilla'],
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0
        ]);
        return $plantilla;
    }
    public function update($data, $plantillaId)
    {
        $plantilla = PlantillaInformacion::findOrFail($plantillaId);
        $plantilla->update($data);
        return $plantilla;
    }
    public function obtenerUno($plantillaId)
    {
        $plantilla = PlantillaInformacion::findOrFail($plantillaId);
        return $plantilla;
    }
    public function listarActivos()
    {
        $plantilla = PlantillaInformacion::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->get();
        return $plantilla;
    }
    public function destroy(PlantillaInformacion $plantilla)
    {
        $plantilla->es_eliminado = 1;
        $plantilla->save();
        return $plantilla;
    }
}
