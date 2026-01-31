<?php

namespace App\Services;

use Carbon\Carbon;
use App\Constants\Estado;
use App\Constants\TipoTransaccion;
use App\Models\BilleteraTransaccion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class BilleteraTransaccionService
{
    protected $billeteraService;

    public function __construct(BilleteraService $billeteraService)
    {
        $this->billeteraService = $billeteraService;
    }

    public function store($data)
    {
        $billeteraTransaccion = BilleteraTransaccion::create([
            'monto' => $data['monto'],
            'fecha_transaccion' => $data['fecha_transaccion'],
            'tipo' => $data['tipo'],
            'glosa' => $data['glosa'],
            'billetera_id' => $data['billetera_id'],
            'orden_id' => $data['orden_id'],
            'usuario_id' => $data['usuario_id'],
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0
        ]);
        return $billeteraTransaccion;
    }
    public function update($data, $transaccionId)
    {
        $billeteraTransaccion = BilleteraTransaccion::findOrFail($transaccionId);
        $billeteraTransaccion->update($data);
        return $billeteraTransaccion;
    }
    public function obtenerUno($transaccionId)
    {
        $billeteraTransaccion = BilleteraTransaccion::findOrFail($transaccionId);
        return $billeteraTransaccion;
    }
    public function listarActivos()
    {
        $billeteraTransaccion = BilleteraTransaccion::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->get();
        return $billeteraTransaccion;
    }
    public function destroy($transaccionId)
    {
        $billeteraTransaccion = BilleteraTransaccion::findOrFail($transaccionId);
        $billeteraTransaccion->es_eliminado = 1;
        $billeteraTransaccion->save();
        return $billeteraTransaccion;
    }

    public function reistroTransaccionBilletera($billeteraId, $monto, $tipoTransaccion, $glosa, $ordenId)
    {
        $idUser = Auth::id();
        $fechaHora = Carbon::now('America/La_Paz')->toDateTimeString();
        $dataTransaccion = [
            'monto' => $monto,
            'fecha_transaccion' => $fechaHora,
            'tipo' => $tipoTransaccion,
            'glosa' => $glosa,
            'billetera_id' => $billeteraId,
            'orden_id' => $ordenId, //Si es un egreso por orden
            'usuario_id' => $idUser,
        ];
        $billeteraTransaccion = $this->store($dataTransaccion);

        //Actualiza saldo de la billetera
        $saldoActualBilletera = $this->billeteraService->obtenerUno($billeteraId);
        //Calcula el nuevo saldo
        if ($tipoTransaccion === TipoTransaccion::CREDITO) {
            $saldoActualizado = $saldoActualBilletera->monto + $monto;
        } else if ($tipoTransaccion === TipoTransaccion::DEBITO) {
            $saldoActualizado = $saldoActualBilletera->monto - $monto;
        }
        $dataBilletera = [
            'monto' => $saldoActualizado
        ];
        $billetera = $this->billeteraService->update($dataBilletera, $billeteraId);

        return $billeteraTransaccion;
    }
    public function obtenerPorOrdenId($ordenId)
    {
        $billeteraTransaccion = BilleteraTransaccion::where('orden_id', $ordenId)->firstOrFail();
        return $billeteraTransaccion;
    }
}
