<?php

namespace App\Models;

use App\Constants\Estado;
use App\Traits\CommonScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tribunal extends Model
{
    use CommonScopes, HasFactory;
    protected $fillable = [
        'expediente',
        'codnurejianuj',
        'link_carpeta',
        'clasetribunal_id',
        'causa_id',
        'juzgado_id',
        'tribunal_dominante',
        'estado',
        'es_eliminado'
    ];

    /**
     * Get the user that owns the Tribunal
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function causa()
    {
        return $this->belongsTo(Causa::class, 'causa_id');
    }
    public function claseTribunal()
    {
        return $this->belongsTo(ClaseTribunal::class, 'clasetribunal_id');
    }
    public function juzgado()
    {
        return $this->belongsTo(Juzgado::class, 'juzgado_id');
    }
    /**
     * Get all of the cuerpoExpedientes for the Tribunal
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cuerpoExpedientes()
    {
        return $this->hasMany(CuerpoExpediente::class, 'tribunal_id')
                    ->where('estado', Estado::ACTIVO)
                    ->where('es_eliminado', 0);
    }
    public function gestionAlternativas()
    {
        return $this->hasMany(GestionAlternativa::class, 'tribunal_id', 'id');
    }
}
