<?php

namespace App\Services;

use App\Constants\Estado;
use App\Constants\TipoTransaccion;
use App\Models\TransaccionesAdmin;
use Illuminate\Http\Request;
use App\Services\TablaConfigService;

class TransaccionesAdminService
{
    protected $tablaConfigService;
    public function __construct(TablaConfigService $tablaConfigService)
    {
        $this->tablaConfigService = $tablaConfigService;
    }

    public function store($data)
    {
        $transaccionesAdmin = TransaccionesAdmin::create([
            'monto' => $data['monto'],
            'fecha_transaccion' => $data['fecha_transaccion'],
            'tipo' => $data['tipo'],
            'transaccion' => $data['transaccion'],
            'glosa' => $data['glosa'],
            'usuario_id' => $data['usuario_id'],
            'billetera_id' => $data['billetera_id'],
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,

        ]);
        return $transaccionesAdmin;
    }
    public function update($data, $transaccionesAdminId)
    {
        $transaccionesAdmin = TransaccionesAdmin::findOrFail($transaccionesAdminId);
        $transaccionesAdmin->update($data);
        return $transaccionesAdmin;
    }
    public function listarActivos()
    {
        $transaccionesAdmin = TransaccionesAdmin::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->get();
        return $transaccionesAdmin;
    }
    public function destroy($transaccionesAdminId)
    {
        $transaccionesAdmin = TransaccionesAdmin::findOrFail($transaccionesAdminId);
        $transaccionesAdmin->es_eliminado = 1;
        $transaccionesAdmin->save();
        return $transaccionesAdmin;
    }
    public function obtenerTransaccionesDeAdmin(Request $request)
    {
        try {
            $query = TransaccionesAdmin::select([
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
            return response()->json(['message' => 'Error al obtener las transacciones de admin.'], 500);
        }
    }
    public function registrarTransaccionAdmin($data)
    {
        $monto = $data['monto'];
        $tipo = $data['tipo'];
        $transaccionesAdmin = $this->store($data);
        //Actualizacion de saldo de la caja del admin
        $cajaAdmin = $this->tablaConfigService->actualizarCajaAdimin($tipo, $monto);

        return $transaccionesAdmin;
    }
}
