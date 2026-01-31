<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CausaPosta extends Model
{
    use HasFactory;
    protected $fillable=[
        'nombre',
        'numero_posta',
        'copia_nombre_plantilla',
        'tiene_informe',
        'causa_id',
        'estado',
        'es_eliminado'
    ];

    /**
     * Get the causa that owns the CausaPosta
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function causa()
    {
        return $this->belongsTo(Causa::class, 'causa_id');
    }

    /**
     * Get all of the comments for the CausaPosta
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function informePosta()
    {
        return $this->hasOne(InformePosta::class, 'causaposta_id');
    }
}
