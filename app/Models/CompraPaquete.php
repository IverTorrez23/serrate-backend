<?php

namespace App\Models;

use App\Constants\Estado;
use App\Traits\CommonScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompraPaquete extends Model
{
    use CommonScopes, HasFactory;
    protected $fillable = [
        'monto',
        'fecha_ini_vigencia',
        'fecha_fin_vigencia',
        'fecha_compra',
        'dias_vigente',
        'paquete_id',
        'usuario_id',
        'estado',
        'es_eliminado'
    ];
    /**
     * Get the user that owns the CompraPaquete
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function paquete()
    {
        return $this->belongsTo(Paquete::class, 'paquete_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
    /**
     * Get all of the comments for the CompraPaquete
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function paqueteCausas()
    {
        return $this->hasMany(PaqueteCausa::class, 'compra_paquete_id')
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0);
    }
}
