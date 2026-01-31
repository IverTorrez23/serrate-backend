<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class RegistroLlamada extends Model
{
    use HasFactory;
    protected $fillable = [
        'numero_telefono',
        'fecha_llamada',
        'gestion_id',
        'usuario_id',
        'estado',
        'es_eliminado'
    ];

    public function gestion()
    {
        return $this->belongsTo(GestionAlternativa::class, 'gestion_id');
    }
}


