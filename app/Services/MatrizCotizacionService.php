<?php
namespace App\Services;

use App\Models\MatrizCotizacion;
use App\Constants\Estado;

class MatrizCotizacionService
{
    public function obtenerIdDePrioridadYCondicion($prioridad, $condicion)
    {
        $matrizCotizacion = MatrizCotizacion::where('es_eliminado', 0)
                                            ->where('estado', Estado::ACTIVO)
                                            ->where('condicion', $condicion)
                                            ->where('numero_prioridad', $prioridad)
                                            ->first();

        return $matrizCotizacion;
    }
    public function obtenerUno($matrizId){
        $matrizCotizacion = MatrizCotizacion::findOrFail($matrizId);
        return $matrizCotizacion;
    }
}
