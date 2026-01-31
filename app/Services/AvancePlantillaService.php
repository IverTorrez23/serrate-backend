<?php

namespace App\Services;

use App\Constants\Estado;
use App\Models\AvancePlantilla;
use Illuminate\Http\Request;

class AvancePlantillaService
{
    public function update($data, $plantillaId)
    {
        $avancePlantilla = AvancePlantilla::findOrFail($plantillaId);
        $avancePlantilla->update($data);
        return $avancePlantilla;
    }
    public function obtenerUno($plantillaId)
    {
        $avancePlantillaId = AvancePlantilla::findOrFail($plantillaId);
        return $avancePlantillaId;
    }
    public function listarActivos()
    {
        $avancePlantilla = AvancePlantilla::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->get();
        return $avancePlantilla;
    }

    public function listarPlantillaPorId($idPlantilla)
    {
        $plantilla = AvancePlantilla::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('id', $idPlantilla)
            ->get();
        return $plantilla;
    }
}
