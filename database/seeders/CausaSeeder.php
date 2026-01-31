<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Constants\EstadoCausa;
use App\Models\Causa;

class CausaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estadoCausa=EstadoCausa::CONGELADA;
        Causa::create([
            'nombre' => 'Causa nueva 1',
            'observacion' => 'es nueva',
            'objetivos'=>'ganar',
            'estrategia'=>'ganarrr',
            'informacion'=>'informacion ',
            'apuntes_juridicos'=>'apuntes jur',
            'apuntes_honorarios'=>' honorarios',
            'tiene_billetera'=>1,
            'billetera'=>0,
            'saldo_devuelto'=>0,
            'color'=>'#E72B5B',
            'materia_id'=>1,
            'tipolegal_id'=>1,
            'categoria_id'=>1,
            'abogado_id'=>2,
            'procurador_id'=>6,
            'usuario_id'=>2,
            'estado' => $estadoCausa,
            'motivo_congelada' => '',
            'es_eliminado' => 0,
        ]);
    }
}
