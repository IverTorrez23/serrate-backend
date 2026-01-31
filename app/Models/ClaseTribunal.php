<?php

namespace App\Models;

use App\Traits\CommonScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaseTribunal extends Model
{
    use CommonScopes, HasFactory;
    protected $fillable = [
        'nombre',
        'estado',
        'es_eliminado'
    ];

    /**
     * Get all of the comments for the ClaseTribunal
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tribunales()
    {
        return $this->hasMany(Tribunal::class, 'clasetribunal_id');
    }
}
