<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatrizCotizacion extends Model
{
    use HasFactory;
    protected $fillable = [
        'numero_prioridad',
        'precio_compra',
        'precio_venta',
        'penalizacion',
        'condicion',
        'estado',
        'es_eliminado'
    ];

    public function ordenes()
    {
        return $this->hasMany(Orden::class, 'matriz_id');
    }
}
