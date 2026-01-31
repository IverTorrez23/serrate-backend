<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CommonScopes;

class Materia extends Model
{
    use CommonScopes, HasFactory;
    protected $fillable = [
        'nombre',
        'abreviatura',
        'estado',
        'es_eliminado'
    ];

    /**
     * Get all of the comments for the Materia
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function TiposLegales()
    {
        return $this->hasMany(TipoLegal::class, 'materia_id');
    }

    /**
     * Get all of the comments for the Materia
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function causas()
    {
        return $this->hasMany(Causa::class, 'materia_id');
    }
}
