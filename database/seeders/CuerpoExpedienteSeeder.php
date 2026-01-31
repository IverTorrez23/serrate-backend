<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Constants\Estado;
use App\Models\CuerpoExpediente;

class CuerpoExpedienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estado=Estado::ACTIVO;
        CuerpoExpediente::create([
            'nombre' => '001-100',
            'link_cuerpo'=>'https://www.calameo.com/read/007304843537935904541',
            'tribunal_id'=>1,
            'estado' => $estado,
            'es_eliminado' => 0,
        ]);
    }
}
