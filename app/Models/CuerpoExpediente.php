<?php

namespace App\Models;

use App\Traits\CommonScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuerpoExpediente extends Model
{
    use CommonScopes, HasFactory;
    protected $fillable=[
        'nombre',
        'link_cuerpo',
        'tribunal_id',
        'estado',
        'es_eliminado'
    ];

    /**
     * Get the tribunal that owns the CuerpoExpediente
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tribunal()
    {
        return $this->belongsTo(Tribunal::class, 'tribunal_id');
    }
    public function gestionAlternativas()
    {
        return $this->hasMany(GestionAlternativa::class, 'cuerpo_expediente_id', 'id');
    }
}
