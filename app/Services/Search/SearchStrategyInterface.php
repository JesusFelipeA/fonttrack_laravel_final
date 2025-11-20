<?php

namespace App\Services\Search;

use Illuminate\Database\Eloquent\Builder;

interface SearchStrategyInterface
{
    public function apply(Builder $query, array $params): Builder;
}

// ============================================
// Estrategia de búsqueda por nombre/texto
// ============================================
class NombreSearchStrategy implements SearchStrategyInterface
{
    public function apply(Builder $query, array $params): Builder
    {
        $term = $params['term'] ?? '';
        $field = $params['field'] ?? 'nombre, correo';

        if (empty($term)) {
            return $query;
        }

        // Búsqueda flexible SOLO en campos que existen en tb_users
        return $query->where(function ($q) use ($term) {
            $q->where('nombre', 'LIKE', "%{$term}%")
              ->orWhere('correo', 'LIKE', "%{$term}%");
            // NO incluir 'descripcion' porque no existe en la tabla usuarios
        });
    }
}

// ============================================
// Estrategia de búsqueda por código exacto
// ============================================
class CodigoSearchStrategy implements SearchStrategyInterface
{
    public function apply(Builder $query, array $params): Builder
    {
        $codigo = $params['codigo'] ?? '';
        $field = $params['field'] ?? 'id_usuario';

        if (empty($codigo)) {
            return $query;
        }

        // Búsqueda exacta por código/ID
        return $query->where($field, '=', $codigo);
    }
}

// ============================================
// Estrategia de búsqueda por rango de fechas
// ============================================
class FechaSearchStrategy implements SearchStrategyInterface
{
    public function apply(Builder $query, array $params): Builder
    {
        $from = $params['from'] ?? null;
        $to = $params['to'] ?? null;
        $dateColumn = $params['date_column'] ?? 'created_at';

        if (empty($from) && empty($to)) {
            return $query;
        }

        if ($from && $to) {
            // Rango completo
            return $query->whereBetween($dateColumn, [$from, $to]);
        } elseif ($from) {
            // Solo desde
            return $query->where($dateColumn, '>=', $from);
        } elseif ($to) {
            // Solo hasta
            return $query->where($dateColumn, '<=', $to);
        }

        return $query;
    }
}