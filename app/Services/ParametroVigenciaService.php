<?php

namespace App\Services;

use App\Constants\Estado;
use App\Constants\FechaHelper;
use App\Models\ParametroVigencia;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ParametroVigenciaService
{
    public function store($data)
    {
        $parametroVigencia = ParametroVigencia::create([
            'fecha_ultima_vigencia' => $data['fecha_ultima_vigencia'],
            'usuario_id' => $data['usuario_id'],
            'esta_vigente' => $data['esta_vigente'],
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0
        ]);
        return $parametroVigencia;
    }

    public function update($data, $parametroVigenciaId)
    {
        $parametroVigencia = ParametroVigencia::findOrFail($parametroVigenciaId);
        $parametroVigencia->update($data);
        return $parametroVigencia;
    }
    public function obtenerUno($parametroVigenciaId)
    {
        $parametroVigencia = ParametroVigencia::findOrFail($parametroVigenciaId);
        return $parametroVigencia;
    }
    public function listarActivos()
    {
        $parametroVigencia = ParametroVigencia::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->get();
        return $parametroVigencia;
    }
    public function obtenerUnoPorUsuario($usuarioId)
    {
        $parametroVigencia = ParametroVigencia::where('usuario_id', $usuarioId)->first();
        return $parametroVigencia;
    }
    public function hayPaqueteVigente($usuarioID): bool
    {
        $parametro = ParametroVigencia::where('usuario_id', $usuarioID)->first();

        if (!$parametro) {
            return false;
        }

        $fechaVigencia = Carbon::parse($parametro->fecha_ultima_vigencia);
        $fechaActualBolivia = Carbon::parse(FechaHelper::fechaHoraBolivia());

        return $fechaVigencia->greaterThan($fechaActualBolivia);
    }
}
