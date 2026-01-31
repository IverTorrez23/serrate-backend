<?php

namespace App\Services;

use App\Constants\Estado;
use App\Models\DevolucionSaldo;
use Illuminate\Http\Request;

class DevolucionSaldoService
{
    public function store($data)
    {
        $devolucion = DevolucionSaldo::create([
            'fecha_devolucion' => $data['fecha_devolucion'],
            'glosa' => $data['glosa'],
            'monto' => $data['monto'],
            'billetera_id' => $data['billetera_id'],
            'usuario_id' => $data['usuario_id'],
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0
        ]);
        return $devolucion;
    }
    public function update($data, $devolucionId)
    {
        $devolucion = DevolucionSaldo::findOrFail($devolucionId);
        $devolucion->update($data);
        return $devolucion;
    }
    public function destroy(DevolucionSaldo $devolucion)
    {
        $devolucion->es_eliminado = 1;
        $devolucion->save();
        return $devolucion;
    }
    public function obtenerDevoluciones(Request $request)
    {
        try {
            $query = DevolucionSaldo::select([
                'id',
                'fecha_devolucion',
                'glosa',
                'monto',
                'billetera_id',
                'usuario_id',
                'estado'
            ])->active()
                ->where('estado', Estado::ACTIVO)
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
            return response()->json(['message' => 'Error al obtener las devoluciones.'], 500);
        }
    }
}
