<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Categoria;
use App\Constants\Estado;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estado=Estado::ACTIVO;
        Categoria::create([
            'nombre' => 'Juzgados Ed. Nago',
            'abreviatura' => 'J-Nago',
            'estado' => $estado,
            'es_eliminado' => 0,
        ]);

        Categoria::create([
            'nombre' => 'Misceláneo',
            'abreviatura' => 'M',
            'estado' => $estado,
            'es_eliminado' => 0,
        ]);

        Categoria::create([
            'nombre' => 'Juzgados de c/ La Paz',
            'abreviatura' => 'J-LaPaz',
            'estado' => $estado,
            'es_eliminado' => 0,
        ]);
    }
}
