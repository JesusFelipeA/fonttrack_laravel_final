<?php

namespace App\Services\Search;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

class BuscarPorCodigoStrategy implements SearchStrategyInterface
{
    public function apply(Builder $query, array $params = []): Builder
    {
        $code = trim($params['term'] ?? '');
        if ($code === '') {
            return $query;
        }

        // obtener tabla del modelo de forma segura
        $model = $query->getModel();
        $table = method_exists($model, 'getTable') ? $model->getTable() : null;

        // campo configurable vía 'field', por defecto 'codigo_barras' o 'clave_material'
        if (!empty($params['field'])) {
            $field = $params['field'];
        } elseif ($table && Schema::hasColumn($table, 'codigo_barras')) {
            $field = 'codigo_barras';
        } else {
            $field = 'clave_material';
        }

        return $query->where($field, $code);
    }
}