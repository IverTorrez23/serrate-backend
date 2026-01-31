<?php

namespace App\Models;

use App\Traits\CommonScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentosCategoria extends Model
{
    use CommonScopes, HasFactory;
    protected $fillable=[
        'nombre',
        'tipo',
        'categoria_id',
        'estado',
        'es_eliminado'
    ];

    // Relación para obtener las subcategorías
    public function children()
    {
        return $this->hasMany(DocumentosCategoria::class, 'categoria_id');
    }
    public function documentos()
    {
        return $this->hasMany(Documento::class, 'categoria_id');
    }

    // Relación para obtener la categoría padre
    public function padre()
    {
        return $this->belongsTo(DocumentosCategoria::class, 'categoria_id');
    }
}
