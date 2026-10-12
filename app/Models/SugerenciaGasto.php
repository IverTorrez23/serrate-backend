<?php

namespace App\Models;

use App\Traits\CommonScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SugerenciaGasto extends Model
{
    use CommonScopes, HasFactory;
    protected $fillable=[
        'detalle',
        'monto',
        'orden_id',
        'usuario_id',
        'estado',
        'es_eliminado'
    ];
}
