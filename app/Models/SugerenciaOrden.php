<?php

namespace App\Models;

use App\Traits\CommonScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SugerenciaOrden extends Model
{
    use CommonScopes, HasFactory;
    protected $fillable=[
        'orden_id',
        'sugerencia_gasto_id',
        'usuario_id',
        'estado',
        'es_eliminado'
    ];
}
