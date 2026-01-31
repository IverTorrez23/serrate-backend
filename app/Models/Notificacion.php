<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CommonScopes;

class Notificacion extends Model
{
    use CommonScopes, HasFactory;
    protected $fillable = [
        'tipo',
        'evento',
        'emisor',
        'nombre_emisor',
        'tipo_receptor',
        'receptor_estatico',
        'descripcion_receptor_estatico',
        'asunto',
        'envia_notificacion',
        'texto',
        'usuario_id',
        'estado',
        'es_eliminado'
    ];
}
