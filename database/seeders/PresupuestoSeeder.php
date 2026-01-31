<?php

namespace Database\Seeders;

use App\Models\Presupuesto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Constants\Estado;
use Carbon\Carbon;

class PresupuestoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now=Carbon::now('America/La_Paz');
        $fechaHora=$now->toDateTimeString();
        Presupuesto::create([
            'monto' => 100,
            'detalle_presupuesto'=>'para gastar',
            'fecha_presupuesto'=>$fechaHora,
            'fecha_entrega'=>null,
            'contador_id'=>5,
            'orden_id'=>1,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
    }
}
