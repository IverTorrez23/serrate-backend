<?php

namespace App\Services;

use App\Constants\Estado;
use App\Models\Juzgado;
use Illuminate\Http\Request;
use App\Models\Piso;

class JuzgadoService
{
    public function store($data)
    {
        $juzgado = Juzgado::create([
            'nombre_numerico' => $data['nombre_numerico'],
            'jerarquia' => $data['jerarquia'],
            'materia_juzgado' => $data['materia_juzgado'],
            'coordenadas' => $data['coordenadas'],
            'foto_url' => $data['foto_url'],
            'contacto1' => $data['contacto1'],
            'contacto2' => $data['contacto2'],
            'contacto3' => $data['contacto3'],
            'contacto4' => $data['contacto4'],
            'distrito_id' => $data['distrito_id'],
            'piso_id' => $data['piso_id'],
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0
        ]);
        return $juzgado;
    }
    public function update($data, $juzgadoId)
    {
        $juzgado = Juzgado::findOrFail($juzgadoId);
        $juzgado->update($data);
        return $juzgado;
    }
    public function obtenerUno($juzgadoId)
    {
        $juzgado = Juzgado::findOrFail($juzgadoId);
        return $juzgado;
    }
    public function listarActivos()
    {
        $juzgados = Juzgado::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->with([
                'distrito',
            ])
            ->get();
        return $juzgados;
    }
}
