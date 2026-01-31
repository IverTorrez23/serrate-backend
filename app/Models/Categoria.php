<?php

namespace App\Models;

use App\Traits\CommonScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use CommonScopes, HasFactory;
    protected $fillable=[
        'nombre',
        'abreviatura',
        'estado',
        'es_eliminado'
    ];

    /**
     * Get all of the comments for the Categoria
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function causas()
    {
        return $this->hasMany(Causa::class, 'categoria_id');
    }
}
