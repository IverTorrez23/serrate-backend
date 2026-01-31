<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Constants\Estado;
use App\Constants\TipoParticipante;
use App\Models\Participante;

class ParticipanteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Participante::create([
            'nombres' => 'Homero Simpson',
            'tipo' => TipoParticipante::DEMANDANTE,
            'foja'=>'089',
            'ultimo_domicilio'=>'Av. siempre viva',
            'causa_id'=>1,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
        Participante::create([
            'nombres' => 'Simmur Skinner',
            'tipo' => TipoParticipante::DEMANDADO,
            'foja'=>'097',
            'ultimo_domicilio'=>'Calle P',
            'causa_id'=>1,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
        Participante::create([
            'nombres' => 'Marge Simpson',
            'tipo' => TipoParticipante::TERCERISTA,
            'foja'=>'21w',
            'ultimo_domicilio'=>'Av. siempre viva',
            'causa_id'=>1,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
    }
}
