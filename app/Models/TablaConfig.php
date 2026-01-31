<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TablaConfig extends Model
{
    use HasFactory;
    protected $fillable = [
        'caja_contador',
        'deuda_extarna',
        'caja_admin',
        'ganancia_procesal_procuraduria',
        'titulo_index',
        'texto_index',
        'imagen_index',
        'imagen_logo',
        'nombre',
        'archivo_url',
        'url_acuerdo_lider',
        'url_acuerdo_indep',
        'url_acuerdo_proc',
        'estado',
        'es_eliminado'
    ];
}
