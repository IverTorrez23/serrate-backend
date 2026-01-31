<?php

namespace App\Services;

use App\Constants\Estado;
use Illuminate\Http\Request;
use App\Models\PaqueteCausa;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PaqueteCausaService
{
    public function listadoPaqueteCausas()
    {
        $paqueteCausas = PaqueteCausa::where('es_eliminado', 0)
            ->where('estado', Estado::ACTIVO)
            ->get();
        return $paqueteCausas;
    }

    public function store($data)
    {
        $paqueteCausa = PaqueteCausa::create([
            'fecha_inicio' => $data['fecha_inicio'],
            'fecha_fin' => $data['fecha_fin'],
            'compra_paquete_id' => $data['compra_paquete_id'],
            'causa_id' => $data['causa_id'],
            'fecha_asociacion' => $data['fecha_asociacion'],
            'usuario_id' => $data['usuario_id'],
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0
        ]);
        return $paqueteCausa;
    }
    public function update($data, $paqueteCausaId)
    {
        $paqueteCausa = PaqueteCausa::findOrFail($paqueteCausaId);
        $paqueteCausa->update($data);
        return $paqueteCausa;
    }
    public function destroy(PaqueteCausa $paqueteCausa)
    {
        $paqueteCausa->es_eliminado = 1;
        $paqueteCausa->save();
        return $paqueteCausa;
    }
    public function obtenerUno($paqueteCausaId)
    {
        $paqueteCausa = PaqueteCausa::find($paqueteCausaId);
        if (!$paqueteCausa) {
            throw new ModelNotFoundException('El paquete causa con ID ' . $paqueteCausaId . ' no existe.');
        }
        return $paqueteCausa;
    }
    public function listadoActivosDeUnPaquete($compraPaqueteId)
    {
        $paqueteCausas = PaqueteCausa::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('compra_paquete_id', $compraPaqueteId)
            ->with([
                'causa',
                'causa.materia',
                'causa.tipoLegal'
            ])
            ->get();
        return $paqueteCausas;
    }
    public function causaEstaEnPaquete($causaId)
    {
        $paqueteCausa = PaqueteCausa::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('causa_id', $causaId)
            ->first();
        if ($paqueteCausa) {
            return true;
        } else {
            return false;
        }
    }
    public function darDeBajaPorCausaId($causaId): bool
    {
        $paqueteCausa = PaqueteCausa::where('causa_id', $causaId)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->first();

        if ($paqueteCausa) {
            $paqueteCausa->es_eliminado = 1;
            return $paqueteCausa->save();
        }
        return false; // Retorna false si no se encontró el registro
    }
}
