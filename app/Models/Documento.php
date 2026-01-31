<?php

namespace App\Models;

use App\Traits\CommonScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    use CommonScopes, HasFactory;
    protected $fillable=[
        'nombre',
        'archivo_url',
        'tipo',
        'categoria_id',
        'estado',
        'es_eliminado'
    ];

    /**
     * Get the user that owns the Documento
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function categoria()
    {
        return $this->belongsTo(DocumentosCategoria::class, 'categoria_id');
    }
}
