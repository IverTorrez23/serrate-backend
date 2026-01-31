<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CommonScopes;

class TipoPosta extends Model
{
    use CommonScopes, HasFactory; 
    protected $fillable=[
        'nombre',
        'estado',
        'es_eliminado'
    ];

    /**
     * Get all of the comments for the TipoPosta
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function informePostas()
    {
        return $this->hasMany(InformePosta::class, 'tipoposta_id');
    }
}
