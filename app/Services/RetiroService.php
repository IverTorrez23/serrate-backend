<?php

namespace App\Services;

use App\Constants\Estado;
use App\Models\Retiro;
use Illuminate\Http\Request;

class RetiroService
{
    public function store($data)
    {
        $retiro = Retiro::create([
            'monto' => $data['monto'],
            'fecha_retiro' => $data['fecha_retiro'],
            'glosa' => $data['glosa'],
            'usuario_id' => $data['usuario_id'],
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0
        ]);
        return $retiro;
    }
    public function update($data, $retiroId)
    {
        $retiro = Retiro::findOrFail($retiroId);
        $retiro->update($data);
        return $retiro;
    }
    public function destroy(Retiro $retiro)
    {
        $retiro->es_eliminado = 1;
        $retiro->save();
        return $retiro;
    }
    public function obtenerRetiros(Request $request)
    {
        try {
            $query = Retiro::select([
                'id',
                'monto',
                'fecha_retiro',
                'glosa',
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
            return response()->json(['message' => 'Error al obtener los retiross.'], 500);
        }
    }
}
