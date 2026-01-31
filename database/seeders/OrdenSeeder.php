<?php

namespace Database\Seeders;

use App\Models\Orden;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Constants\Estado;
use App\Constants\EtapaOrden;
use App\Constants\TipoUsuarioOrden;
use Carbon\Carbon;

class OrdenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now=Carbon::now('America/La_Paz');
        $fechaHora=$now->toDateTimeString();

        $now = Carbon::now('America/La_Paz');
        $fechafin = $now->addHours(97)->toDateTimeString();
        Orden::create([
            'entrega_informacion' => 'entrega de informacion',
            'entrega_documentacion' => 'entrega de documentacion',
            'fecha_inicio'=>$fechaHora,
            'fecha_fin'=>$fechafin,
            'fecha_giro'=>$fechaHora,
            'plazo_hora'=>'97',
            'fecha_recepcion'=>null,
            'etapa_orden'=>EtapaOrden::GIRADA,
            'calificacion'=>'',
            'prioridad'=>1,
            'fecha_cierre'=>null,
            'girada_por'=>TipoUsuarioOrden::ABOGADO,
            'fecha_ini_bandera'=>$fechaHora,
            'notificado'=>0,
            'lugar_ejecucion'=>'Juzgado Adquo',
            'sugerencia_presupuesto'=>'yo procurador sugiero 30 bs',
            'tiene_propina'=>1,
            'propina'=>20,
            'causa_id'=>1,
            'procurador_id'=>6,
            'matriz_id'=>1,
            'usuario_id' => 2,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
    }
}
