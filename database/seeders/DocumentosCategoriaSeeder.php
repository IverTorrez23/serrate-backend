<?php

namespace Database\Seeders;

use App\Constants\Estado;
use App\Constants\TipoDocumento;
use App\Models\Documento;
use App\Models\DocumentosCategoria;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentosCategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $docCat=DocumentosCategoria::create([
            'nombre' => 'Tramites Santa Cruz',
            'tipo' => TipoDocumento::TRAMITES,
            'categoria_id' => 0,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
        Documento::create([
            'nombre' => 'Archivo pdf tramites de 2024',
            'archivo_url' => 'doc_tramites_vigentes.pdf',
            'tipo' => TipoDocumento::TRAMITES,
            'categoria_id' => $docCat->id,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
        Documento::create([
            'nombre' => 'Segundo Archivo pdf tramites de 2024',
            'archivo_url' => 'doc_vigentes2.pdf',
            'tipo' => TipoDocumento::TRAMITES,
            'categoria_id' => $docCat->id,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);



        $subCat= DocumentosCategoria::create([
            'nombre' => 'Tramites Palacio de Justicia SCZ',
            'tipo' => TipoDocumento::TRAMITES,
            'categoria_id' => $docCat->id,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
        Documento::create([
            'nombre' => 'Tramites Palacio scz',
            'archivo_url' => 'doc_palacio1.pdf',
            'tipo' => TipoDocumento::TRAMITES,
            'categoria_id' => $subCat->id,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
        Documento::create([
            'nombre' => 'Tramites 2 Palacio scz',
            'archivo_url' => 'doc_palacio2.pdf',
            'tipo' => TipoDocumento::TRAMITES,
            'categoria_id' => $subCat->id,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);

    }
}
