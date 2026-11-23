<?php

namespace App\Models;

use App\Traits\CommonScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlantillaInformacion extends Model
{
    use CommonScopes, HasFactory;
    protected $fillable=[
        'nombre',
        'cuerpo_plantilla',
        'estado',
        'es_eliminado'
    ];
}
