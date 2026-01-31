<?php

namespace App\Traits;

use App\Constants\EstadoCausa;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait CommonsScopesCausa
{
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('es_eliminado', 0)
            ->whereIn('estado', [EstadoCausa::ACTIVA, EstadoCausa::CONGELADA]);
    }

    public function scopeSearch(Builder $query, array $search): Builder
    {
        foreach ($search as $searchItem) {
            $query->where(function ($q) use ($searchItem) {
                foreach ($searchItem['fields'] as $field) {
                    $q->orWhere($field, 'LIKE', '%' . $searchItem['keyword'] . '%');
                }
            });
        }
        return $query;
    }

    public function scopeSort(Builder $query, array $sort): Builder
    {
        foreach ($sort as $sortItem) {
            $query->orderBy($sortItem['field'], $sortItem['orderType']);
        }
        return $query;
    }
}
