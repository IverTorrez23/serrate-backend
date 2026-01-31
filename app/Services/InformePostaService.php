<?php

namespace App\Services;

use App\Constants\Estado;
use App\Models\InformePosta;
use Illuminate\Http\Request;

class InformePostaService
{
    public function store($data)
    {
        $informePosta = InformePosta::create([
            'foja_informe' => $data['foja_informe'],
            'fecha_informe' => $data['fecha_informe'],
            'calculo_gasto' => $data['calculo_gasto'],
            'honorario_informe' => $data['honorario_informe'],
            'foja_truncamiento' => $data['foja_truncamiento'],
            'fecha_truncamiento' => $data['fecha_truncamiento'],
            'honorario_informe_truncamiento' => $data['honorario_informe_truncamiento'],
            'esta_escrito' => $data['esta_escrito'],
            'tipoposta_id' => $data['tipoposta_id'],
            'causaposta_id' => $data['causaposta_id'],
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0
        ]);
        return $informePosta;
    }
    public function update($data, $informeId)
    {
        $informePosta = InformePosta::findOrFail($informeId);
        $informePosta->update($data);
        return $informePosta;
    }
    public function obtenerUno($informeId)
    {
        $informePosta = InformePosta::findOrFail($informeId);
        return $informePosta;
    }
    public function listarActivos()
    {
        $informePosta = InformePosta::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->get();
        return $informePosta;
    }
}
