<?php

namespace App\Services\Search;

use Illuminate\Database\Eloquent\Builder;

class BuscarPorFechaStrategy implements SearchStrategyInterface
{
    public function apply(Builder $query, array $params = []): Builder
    {
        $from = $params['from'] ?? null;
        $to   = $params['to'] ?? null;
        $dateColumn = $params['date_column'] ?? 'created_at';

        if ($from && $to) {
            return $query->whereBetween($dateColumn, [$from, $to]);
        }

        if ($from) {
            return $query->where($dateColumn, '>=', $from);
        }

        if ($to) {
            return $query->where($dateColumn, '<=', $to);
        }

        return $query;
    }
}