<?php

namespace App\Models;

use App\Traits\CommonScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Distrito extends Model
{
    use CommonScopes, HasFactory;
    protected $fillable=[
        'nombre',
        'abreviatura',
        'estado',
        'es_eliminado'
    ];

    /**
     * Get all of the comments for the Distrito
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function juzgados()
    {
        return $this->hasMany(Juzgado::class, 'distrito_id');
    }
}
