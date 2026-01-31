<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CommonScopes;

class Paquete extends Model
{
    use CommonScopes, HasFactory;
    protected $fillable = [
        'nombre',
        'precio',
        'cantidad_dias',
        'descripcion',
        'fecha_creacion',
        'usuario_id',
        'tiene_fecha_limite',
        'fecha_limite_compra',
        'tipo',
        'estado',
        'es_eliminado'
    ];

    /**
     * Get all of the comments for the Paquete
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function compraPaquetes()
    {
        return $this->hasMany(CompraPaquete::class, 'paquete_id');
    }
}
