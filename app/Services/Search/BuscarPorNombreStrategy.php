<?php

namespace App\Services\Search;

use Illuminate\Database\Eloquent\Builder;

class BuscarPorNombreStrategy implements SearchStrategyInterface
{
    public function apply(Builder $query, array $params = []): Builder
    {
        $term = trim($params['term'] ?? '');
        if ($term === '') {
            return $query;
        }

        // busca en campos comunes: nombre, descripcion, clave_material
        return $query->where(function (Builder $q) use ($term) {
            $q->where('nombre', 'like', "%{$term}%")
              ->orWhere('descripcion', 'like', "%{$term}%")
              ->orWhere('clave_material', 'like', "%{$term}%");
        });
    }
}