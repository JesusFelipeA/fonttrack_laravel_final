<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use App\Services\Search\SearchStrategyInterface;

class Buscador
{
    protected array $strategies;

    /**
     * Recibe array asociativo ['nombre'=>Strategy, 'codigo'=>Strategy, ...]
     */
    public function __construct(array $strategies = [])
    {
        $this->strategies = $strategies;
    }

    /**
     * Aplica la estrategia sobre el query builder y devuelve el builder resultante.
     *
     * @param Builder $query
     * @param string $strategyKey
     * @param array $params
     * @return Builder
     */
    public function search(Builder $query, string $strategyKey, array $params = []): Builder
    {
        // Verificar si la estrategia existe
        if (!isset($this->strategies[$strategyKey])) {
            // Si no existe la estrategia solicitada, verificar si existe 'nombre' como fallback
            if (isset($this->strategies['nombre'])) {
                $strategyKey = 'nombre';
            } else {
                // Si tampoco existe 'nombre', retornar el query sin modificar
                return $query;
            }
        }

        $strategy = $this->strategies[$strategyKey];

        // Verificar que la estrategia implemente la interfaz correcta
        if (!$strategy instanceof SearchStrategyInterface) {
            return $query;
        }

        return $strategy->apply($query, $params);
    }

    /**
     * Obtener todas las estrategias disponibles
     *
     * @return array
     */
    public function getAvailableStrategies(): array
    {
        return array_keys($this->strategies);
    }

    /**
     * Verificar si una estrategia existe
     *
     * @param string $strategyKey
     * @return bool
     */
    public function hasStrategy(string $strategyKey): bool
    {
        return isset($this->strategies[$strategyKey]);
    }

    /**
     * Agregar o reemplazar una estrategia
     *
     * @param string $key
     * @param SearchStrategyInterface $strategy
     * @return void
     */
    public function addStrategy(string $key, SearchStrategyInterface $strategy): void
    {
        $this->strategies[$key] = $strategy;
    }
}