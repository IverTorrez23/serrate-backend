<?php

namespace App\Services;

use App\Constants\Estado;
use App\Models\Billetera;
use Illuminate\Http\Request;

class BilleteraService
{
    public function store($data)
    {
        $billetera = Billetera::create([
            'monto' => $data['monto'],
            'abogado_id' => $data['abogado_id'],
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0
        ]);
        return $billetera;
    }
    public function update($data, $billeteraId)
    {
        $billetera = Billetera::findOrFail($billeteraId);
        $billetera->update($data);
        return $billetera;
    }
    public function obtenerUno($billeteraId)
    {
        $billetera = Billetera::findOrFail($billeteraId);
        return $billetera;
    }
    public function listarActivos()
    {
        $billetera = Billetera::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->get();
        return $billetera;
    }
    public function obtenerUnoPorAbogadoId($abogadoId)
    {
        $billetera = Billetera::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('abogado_id', $abogadoId)
            ->first();
        return $billetera;
    }
    public function destroy($billeteraId)
    {
        $billetera = Billetera::findOrFail($billeteraId);
        $billetera->es_eliminado = 1;
        $billetera->save();
        return $billetera;
    }
    public function listarConUsuarios()
    {
        return Billetera::select(
            'id',
            'monto',
            'abogado_id'
        )
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->with([
                'abogado:id,name',
                'abogado.persona:id,nombre,apellido,usuario_id'
            ])
            ->get();
    }
}
