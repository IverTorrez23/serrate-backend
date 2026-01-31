<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Constants\Estado;
use App\Models\AgendaApunte;
use Carbon\Carbon;

class AgendaApunteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now=Carbon::now('America/La_Paz');
        $fechaHora=$now->toDateTimeString();
        AgendaApunte::create([
            'detalle_apunte' => 'Hablar e instar al procurador, de que atienda esta orden.',
            'fecha_inicio' => $fechaHora,
            'fecha_fin' => $fechaHora,
            'color' => '#F5EB0F',
            'causa_id' => 1,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
    }
}
