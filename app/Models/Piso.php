<?php

namespace App\Models;

use App\Traits\CommonScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Piso extends Model
{
    use CommonScopes, HasFactory;
    protected $fillable=[
        'nombre',
        'estado',
        'es_eliminado'
    ];

    /**
     * Get all of the comments for the Piso
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function juzgados()
    {
        return $this->hasMany(Juzgado::class, 'piso_id');
    }
}
