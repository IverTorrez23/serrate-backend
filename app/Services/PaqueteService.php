<?php

namespace App\Services;

use Carbon\Carbon;
use App\Constants\Estado;
use App\Constants\FechaHelper;
use App\Constants\TipoUsuario;
use Illuminate\Http\Request;
use App\Models\Paquete;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PaqueteService
{
    public function listadoPaquetes()
    {
        return Paquete::where('es_eliminado', 0)
            ->where('estado', Estado::ACTIVO)
            ->where(function ($query) {
                $query->where('tiene_fecha_limite', 0)
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('tiene_fecha_limite', 1)
                            ->whereDate('fecha_limite_compra', '>=', FechaHelper::soloFechaBolivia());
                    });
            })
            ->get();
    }

    public function store($data)
    {
        $paquete = Paquete::create([
            'nombre' => $data['nombre'],
            'precio' => $data['precio'],
            'cantidad_dias' => $data['cantidad_dias'],
            'descripcion' => $data['descripcion'],
            'fecha_creacion' => $data['fecha_creacion'],
            'usuario_id' => $data['usuario_id'],
            'tiene_fecha_limite' => $data['tiene_fecha_limite'],
            'fecha_limite_compra' => $data['fecha_limite_compra'],
            'tipo' => $data['tipo'],
            'es_promocion' => $data['es_promocion'],
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0
        ]);
        return $paquete;
    }
    public function update($data, $paqueteId)
    {
        $paquete = Paquete::findOrFail($paqueteId);
        $paquete->update($data);
        return $paquete;
    }
    public function destroy(Paquete $paquete)
    {
        $paquete->es_eliminado = 1;
        $paquete->save();
        return $paquete;
    }
    public function obtenerUno($paqueteId)
    {
        $paquete = Paquete::find($paqueteId);
        if (!$paquete) {
            throw new ModelNotFoundException('El paquete con ID ' . $paqueteId . ' no existe.');
        }
        return $paquete;
    }
    public function obtenerUnoPromocion($paqueteId)
    {
        $paquete = Paquete::query()
            ->where('id', $paqueteId)
            ->where('es_promocion', 1)
            ->first();
        if (!$paquete) {
            throw new ModelNotFoundException('El paquete con ID ' . $paqueteId . ' no existe.');
        }
        return $paquete;
    }
    public function listadoPaquetesParaLider()
    {
        $fechaActual = Carbon::now()->format('Y-m-d');
        $paquete = Paquete::where('es_eliminado', 0)
            ->where('estado', Estado::ACTIVO)
            ->where('tipo', TipoUsuario::ABOGADO_LIDER)
            ->where('es_promocion', 0)
            ->where(function ($query) use ($fechaActual) {
                $query->whereNull('fecha_limite_compra')
                    ->orWhere('fecha_limite_compra', '>=', $fechaActual);
            })
            ->get();
        return $paquete;
    }
    public function listadoPaquetesParaIndependiente()
    {
        $fechaActual = Carbon::now()->format('Y-m-d');
        $paquete = Paquete::where('es_eliminado', 0)
            ->where('estado', Estado::ACTIVO)
            ->where('tipo', TipoUsuario::ABOGADO_INDEPENDIENTE)
            ->where('es_promocion', 0)
            ->where(function ($query) use ($fechaActual) {
                $query->whereNull('fecha_limite_compra')
                    ->orWhere('fecha_limite_compra', '>=', $fechaActual);
            })
            ->get();
        return $paquete;
    }
}
