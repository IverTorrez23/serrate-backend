<?php

namespace App\Models;

use App\Traits\CommonScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    use CommonScopes, HasFactory;
    protected $fillable = [
        'nombre',
        'apellido',
        'telefono',
        'direccion',
        'coordenadas',
        'observacion',
        'foto_url',
        'estado',
        'es_eliminado',
        'usuario_id',
    ];

    /**
     * Get the user that owns the Persona
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
