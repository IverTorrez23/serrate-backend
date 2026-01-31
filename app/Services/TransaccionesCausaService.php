<?php

namespace App\Services;

use App\Constants\Estado;
use App\Constants\EstadoCausa;
use App\Constants\TipoTransaccion;
use App\Constants\Transaccion;
use App\Constants\TransaccionCausa;
use App\Models\Causa;
use App\Models\TransaccionesCausa;
use Illuminate\Http\Request;
use App\Services\CausaService;

class TransaccionesCausaService
{
    protected $causaService;

    public function __construct(CausaService $causaService)
    {
        $this->causaService = $causaService;
    }
    public function store($data)
    {
        $transaccionesCausa = TransaccionesCausa::create([
            'monto' => $data['monto'],
            'fecha_transaccion' => $data['fecha_transaccion'],
            'tipo' => $data['tipo'],
            'transaccion' => $data['transaccion'],
            'glosa' => $data['glosa'],
            'causa_id' => $data['causa_id'],
            'causa_origen_destino' => $data['causa_origen_destino'],
            'orden_id' => $data['orden_id'],
            'usuario_id' => $data['usuario_id'],
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,

        ]);
        return $transaccionesCausa;
    }
    public function update($data, $transaccionesCausaId)
    {
        $transaccionesCausa = TransaccionesCausa::findOrFail($transaccionesCausaId);
        $transaccionesCausa->update($data);
        return $transaccionesCausa;
    }
    public function listarActivos()
    {
        $transaccionesCausa = TransaccionesCausa::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->get();
        return $transaccionesCausa;
    }
    public function destroy($transaccionesCausaId)
    {
        $transaccionesCausa = TransaccionesCausa::findOrFail($transaccionesCausaId);
        $transaccionesCausa->es_eliminado = 1;
        $transaccionesCausa->save();
        return $transaccionesCausa;
    }
    public function obtenerUno($transaccionesCausaId)
    {
        $transaccionesCausa = TransaccionesCausa::findOrFail($transaccionesCausaId);
        return $transaccionesCausa;
    }
    public function obtenerTransaccionesDeCausa(Request $request, $causaId)
    {
        try {
            $query = TransaccionesCausa::select([
                'id',
                'monto',
                'fecha_transaccion',
                'tipo',
                'transaccion',
                'glosa',
                'causa_id',
                'causa_origen_destino',
                'estado'
            ])->active()
                ->where('estado', Estado::ACTIVO)
                ->where('es_eliminado', 0);
            $query->where('causa_id', $causaId);

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
            return response()->json(['message' => 'Error al obtener las transacciones de causa.'], 500);
        }
    }
    public function registrarTransaccionCausa($data)
    {
        $causaId = $data['causa_id'];
        $monto = $data['monto'];
        $tipo = $data['tipo'];
        $causa = Causa::findOrFail($causaId);
        $transaccionesCausa = $this->store($data);
        //Actualizacion de saldo de la billetera de causa
        if ($tipo === TipoTransaccion::CREDITO) {
            $saldoActualizadoCausa = $causa->billetera + $monto;
            $dataCausa = [
                'billetera' => $saldoActualizadoCausa
            ];
            $causa = $this->causaService->update($dataCausa, $causa->id);
            //Se activa la causa, si esta congelada
            if ($causa->estado === EstadoCausa::BLOQUEADA) {
                $this->causaService->activarCausa($causaId);
            }
        } else {
            if ($tipo === TipoTransaccion::DEBITO) {
                $saldoActualizadoCausa = $causa->billetera - $monto;
                $dataCausa = [
                    'billetera' => $saldoActualizadoCausa
                ];
                $causa = $this->causaService->update($dataCausa, $causa->id);
            }
        }
        return $transaccionesCausa;
    }
    public function obtenerPorOrdenId($ordenId)
    {
        $transaccionesCausa = TransaccionesCausa::where('orden_id', $ordenId)->firstOrFail();
        return $transaccionesCausa;
    }
    public function obtenerDepositosDeCausa(Request $request, $causaId)
    {
        try {
            $query = TransaccionesCausa::select([
                'id',
                'monto',
                'fecha_transaccion',
                'tipo',
                'transaccion',
                'glosa',
                'causa_id',
                'causa_origen_destino',
                'estado'
            ])->active()
                ->where('estado', Estado::ACTIVO)
                ->where('es_eliminado', 0)
                ->where('tipo', TipoTransaccion::CREDITO)
                ->where('transaccion', TransaccionCausa::DEPOSITO);
            $query->where('causa_id', $causaId);

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
            return response()->json(['message' => 'Error al obtener las transacciones de causa.'], 500);
        }
    }
    public function trnEnvioRecibidoCausa($causaId)
    {
        return TransaccionesCausa::query()
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->whereIn('transaccion', [
                TransaccionCausa::TRANSFERENCIA_ENVIADA,
                TransaccionCausa::TRANSFERENCIA_RECIBIDA
            ])
            ->where('causa_id', '=', $causaId)
            // Ordenadas por id ascendente
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($trnCausa) {
                return [
                    'id'                  => $trnCausa->id,
                    'monto'        => $trnCausa->monto,
                    'fecha_transaccion' => $trnCausa->fecha_transaccion,
                    'tipo'           => $trnCausa->tipo,
                    'transaccion'        => $trnCausa->transaccion,
                    'glosa'        => $trnCausa->glosa,
                    'causa_id' => $trnCausa->causa_id,
                    'causa_origen_destino' => $trnCausa->causa_origen_destino,
                    'orden_id' => $trnCausa->orden_id,
                    'usuario_id' => $trnCausa->usuario_id,
                    'estado' => $trnCausa->estado,
                ];
            });
    }
}
