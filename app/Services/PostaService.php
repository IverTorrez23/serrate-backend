<?php

namespace App\Services;

use App\Constants\Estado;
use App\Models\Posta;

class PostaService
{

  public function index($request)
  {
    return $this->getPostas($request);
  }

  public function listarPorIdPlantilla($request, $idPlantilla)
  {
    return $this->getPostas($request, $idPlantilla);
  }


  private function getPostas($request, $idPlantilla = null)
  {
    $query = Posta::active();

    if ($idPlantilla) {
      $query->where('plantilla_id', $idPlantilla);
    }

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
  }


  public function show()
  {
    return Posta::active()->get();
  }


  public function store($data)
  {
    $maxNumeroPosta = Posta::where('plantilla_id', $data['plantilla_id'])
      ->where('es_eliminado', 0)
      ->max('numero_posta');


    $nextNumeroPosta = $maxNumeroPosta ? $maxNumeroPosta + 1 : 1;


    return Posta::create([
      'nombre' => $data['nombre'],
      'numero_posta' => $nextNumeroPosta,
      'plantilla_id' => $data['plantilla_id'],
      'estado' => Estado::ACTIVO,
      'es_eliminado' => 0
    ]);
  }


  public function update($data, $postaId)
  {
    $posta = Posta::findOrFail($postaId);
    $posta->update($data);
    return $posta;
  }

  public function destroy($postaId)
  {
    $posta = Posta::findOrFail($postaId);
    $posta->es_eliminado = 1;
    $posta->save();
    return $posta;
  }



  public function listarPorAvancePlantillaId($avancePlantillaId)
  {
    $postas = Posta::Active()
      ->where('plantilla_id', $avancePlantillaId)
      ->orderBy('numero_posta', 'asc')
      ->get();
    return $postas;
  }
  public function listarActivasPorPlantillaId($avancePlantillaId)
  {
    $postas = Posta::where('plantilla_id', $avancePlantillaId)
      ->where('estado', Estado::ACTIVO)
      ->where('es_eliminado', 0)
      ->orderBy('numero_posta', 'asc')
      ->get();
    return $postas;
  }
}
