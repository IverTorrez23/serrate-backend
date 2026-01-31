<?php

namespace App\Services;

use App\Constants\Estado;
use App\Models\CausaPosta;
use App\Models\InformePosta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CausaPostaService
{
    protected $avancePlantillaService;
    protected $postaService;
    public function __construct(AvancePlantillaService $avancePlantillaService, PostaService $postaService)
    {
        $this->avancePlantillaService = $avancePlantillaService;
        $this->postaService = $postaService;
    }
    public function store($data)
    {
        $causaPosta = CausaPosta::create([
            'nombre' => $data['nombre'],
            'numero_posta' => $data['numero_posta'],
            'copia_nombre_plantilla' => $data['copia_nombre_plantilla'],
            'tiene_informe' => $data['tiene_informe'],
            'causa_id' => $data['causa_id'],
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0
        ]);
        return $causaPosta;
    }
    public function update($data, $causaPostaId)
    {
        $causaPosta = CausaPosta::findOrFail($causaPostaId);
        $causaPosta->update($data);
        return $causaPosta;
    }
    public function eliminarPorCausaId($causaId)
    {
        CausaPosta::where('causa_id', $causaId)
            ->update([
                'estado' => Estado::INACTIVO,
                'es_eliminado' => 1
            ]);
    }
    public function listadoActivosPorCausa($causaId)
    {
        $causaPostas = CausaPosta::with(['informePosta' => function ($query) {
            $query->where('estado', Estado::ACTIVO)
                ->where('es_eliminado', 0);
        }])
            ->where('causa_id', $causaId)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->orderBy('numero_posta', 'asc')
            ->get();
        return $causaPostas;
    }
    public function eliminarTodoPorCausa(int $causaId): array
    {
        return DB::transaction(function () use ($causaId) {
            // 1) Obtener IDs de CausaPosta de la causa
            $causaPostaIds = CausaPosta::where('causa_id', $causaId)->pluck('id');

            if ($causaPostaIds->isEmpty()) {
                return ['informes_eliminados' => 0, 'causa_postas_eliminados' => 0];
            }

            // 2) Borrar Informes relacionados
            $informesEliminados = InformePosta::whereIn('causaposta_id', $causaPostaIds)->delete();

            // 3) Borrar CausaPosta
            $causaPostasEliminados = CausaPosta::whereIn('id', $causaPostaIds)->delete();

            return [
                'informes_eliminados' => $informesEliminados,
                'causa_postas_eliminados' => $causaPostasEliminados,
            ];
        });
    }
}
