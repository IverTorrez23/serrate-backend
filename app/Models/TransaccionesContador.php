<?php

namespace App\Models;

use App\Traits\CommonScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaccionesContador extends Model
{
    use CommonScopes, HasFactory;
    protected $fillable=[
        'monto',
        'fecha_transaccion',
        'tipo',
        'transaccion',
        'glosa',
        'contador_id',
        'usuario_id',
        'estado',
        'es_eliminado'
    ];
}
