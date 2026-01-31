<?php

namespace App\Services;

use App\Constants\Estado;
use App\Constants\TipoTransaccion;
//use App\Models\TransaccionesAdmin;
use Illuminate\Http\Request;
use App\Services\TablaConfigService;
use App\Models\TransaccionesContador;

class TransaccionesContadorService
{
    protected $tablaConfigService;
    public function __construct(TablaConfigService $tablaConfigService)
    {
        $this->tablaConfigService = $tablaConfigService;
    }

    public function store($data)
    {
        $transaccionesContador = TransaccionesContador::create([
            'monto' => $data['monto'],
            'fecha_transaccion' => $data['fecha_transaccion'],
            'tipo' => $data['tipo'],
            'transaccion' => $data['transaccion'],
            'glosa' => $data['glosa'],
            'contador_id' => $data['contador_id'],
            'usuario_id' => $data['usuario_id'],
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,

        ]);
        return $transaccionesContador;
    }
    public function update($data, $transaccionesContadorId)
    {
        $transaccionesContador = TransaccionesContador::findOrFail($transaccionesContadorId);
        $transaccionesContador->update($data);
        return $transaccionesContador;
    }
    public function listarActivos()
    {
        $transaccionesContador = TransaccionesContador::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->get();
        return $transaccionesContador;
    }
    public function destroy($transaccionesContadorId)
    {
        $transaccionesContador = TransaccionesContador::findOrFail($transaccionesContadorId);
        $transaccionesContador->es_eliminado = 1;
        $transaccionesContador->save();
        return $transaccionesContador;
    }
    public function obtenerTransaccionesContador(Request $request)
    {
        try {
            $query = TransaccionesContador::select([
                'id',
                'monto',
                'fecha_transaccion',
                'tipo',
                'transaccion',
                'glosa',
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

            //$result = $query->get();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener las transacciones de contador.'], 500);
        }
    }
    public function registrarTransaccionContador($data)
    {
        $monto = $data['monto'];
        $tipo = $data['tipo'];
        $transaccionesContador = $this->store($data);
        //Actualizacion de saldo de la caja del contador
        $cajaContador = $this->tablaConfigService->actualizarCajaContador($tipo, $monto);

        return $transaccionesContador;
    }
}
