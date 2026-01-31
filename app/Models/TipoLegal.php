<?php

namespace App\Models;

use App\Traits\CommonScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoLegal extends Model
{
    use CommonScopes, HasFactory;
    protected $fillable=[
        'nombre',
        'abreviatura',
        'estado',
        'es_eliminado',
        'materia_id',
    ];

    /**
     * Get the user that owns the TipoLegal
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function materia()
    {
        return $this->belongsTo(Materia::class,'materia_id');
    }

    /**
     * Get all of the comments for the TipoLegal
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function causas()
    {
        return $this->hasMany(Causa::class, 'tipolegal_id');
    }
}
