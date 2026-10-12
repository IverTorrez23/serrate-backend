<?php

namespace App\Services;

use App\Constants\Estado;
use App\Models\SugerenciaGasto;
use Illuminate\Http\Request;

class SugerenciaGastoService
{
    public function store($data)
    {
        $sugerencia = SugerenciaGasto::create([
            'detalle' => $data['detalle'],
            'monto' => $data['monto'],
            'orden_id' => $data['orden_id'],
            'usuario_id' => $data['usuario_id'],
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0
        ]);
        return $sugerencia;
    }
    public function update($data, $sugerenciaId)
    {
        $sugerencia = SugerenciaGasto::findOrFail($sugerenciaId);
        $sugerencia->update($data);
        return $sugerencia;
    }
    public function destroy(SugerenciaGasto $sugerencia)
    {
        $sugerencia->es_eliminado = 1;
        $sugerencia->save();
        return $sugerencia;
    }
    public function obtenerSugerenciaGasto(Request $request)
    {
        try {
            $query = SugerenciaGasto::select([
                'id',
                'detalle',
                'monto',
                'orden_id',
                'usuario_id',
                'estado'
            ])->active()
                ->where('estado', Estado::ACTIVO)
                ->where('orden_id', 0)
                ->where('es_eliminado', 0);

            if ($request->has('search')) {
                $search = json_decode($request->input('search'), true);
                $query->search($search);
            }

            if ($request->has('sort')) {
                $sort = json_decode($request->input('sort'), true);
                $query->sort($sort);
            }

            $perPage = $request->input('perPage', 10);
            return $query->paginate($perPage);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener las sugerencia de gastos.'], 500);
        }
    }
    public function listarActivosGenerales()
    { 
        return SugerenciaGasto::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('orden_id', 0)
            ->orderBy('detalle', 'ASC')
            ->get();
    }
    public function listarActivosYConUnaOrden($ordenId)
    {
        return SugerenciaGasto::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->whereIn('orden_id', [0, $ordenId])
            ->orderBy('detalle', 'ASC')
            ->get();
    }
}
