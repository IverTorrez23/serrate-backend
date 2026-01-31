<?php

namespace App\Services;

use App\Constants\Estado;
use App\Models\ProcuradorPago;
use Illuminate\Http\Request;

class ProcuradorPagoService
{
    public function store($data)
    {
        $procuradorPago = ProcuradorPago::create([
            'monto' => $data['monto'],
            'tipo' => $data['tipo'],
            'fecha_pago' => $data['fecha_pago'],
            'fecha_inicio_consulta' => $data['fecha_inicio_consulta'],
            'fecha_fin_consulta' => $data['fecha_fin_consulta'],
            'glosa' => $data['glosa'],
            'procurador_id' => $data['procurador_id'],
            'usuario_id' => $data['usuario_id'],
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0
        ]);
        return $procuradorPago;
    }
    public function update($data, $procuradorPagoId)
    {
        $procuradorPago = ProcuradorPago::findOrFail($procuradorPagoId);
        $procuradorPago->update($data);
        return $procuradorPago;
    }
    public function destroy(ProcuradorPago $procuradorPago)
    {
        $procuradorPago->es_eliminado = 1;
        $procuradorPago->save();
        return $procuradorPago;
    }
    public function obtenerUnoPorProcuradorId($procuradorId)
    {
        $procuradorPago = ProcuradorPago::where('procurador_id', $procuradorId)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->first();
        return $procuradorPago;
    }
    public function obtenerPagosAProcuradores(Request $request)
    {
        try {
            $query = ProcuradorPago::select([
                'id',
                'monto',
                'tipo',
                'fecha_pago',
                'fecha_inicio_consulta',
                'fecha_fin_consulta',
                'glosa',
                'procurador_id',
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
            return response()->json(['message' => 'Error al obtener los pagos a procuradores.'], 500);
        }
    }

    public function obtenerUltimoPagoDeProcurador($procuradorId)
    {
        $procuradorPago = ProcuradorPago::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('procurador_id', $procuradorId)
            ->latest()
            ->first();
        return $procuradorPago;
    }
    public function obtenerPagosDeUnProcurador(Request $request, $procuradorId)
    {
        try {
            $query = ProcuradorPago::select([
                'id',
                'monto',
                'tipo',
                'fecha_pago',
                'fecha_inicio_consulta',
                'fecha_fin_consulta',
                'glosa',
                'procurador_id',
                'usuario_id',
                'estado'
            ])->active()
                ->where('procurador_id', $procuradorId)
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
            return response()->json(['message' => 'Error al obtener los pagos a procuradores.'], 500);
        }
    }
}
