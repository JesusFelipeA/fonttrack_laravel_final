<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Buscador;
use App\Services\Search\BuscarPorNombreStrategy;
use App\Services\Search\BuscarPorCodigoStrategy;
use App\Services\Search\BuscarPorFechaStrategy;

class SearchServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Bindings de estrategias
        $this->app->bind(BuscarPorNombreStrategy::class, fn($app) => new BuscarPorNombreStrategy());
        $this->app->bind(BuscarPorCodigoStrategy::class, fn($app) => new BuscarPorCodigoStrategy());
        $this->app->bind(BuscarPorFechaStrategy::class, fn($app) => new BuscarPorFechaStrategy());

        // Singleton Buscador con estrategias configuradas
        $this->app->singleton(Buscador::class, function ($app) {
            return new Buscador([
                'nombre' => $app->make(BuscarPorNombreStrategy::class),
                'codigo' => $app->make(BuscarPorCodigoStrategy::class),
                'fecha'  => $app->make(BuscarPorFechaStrategy::class),
            ]);
        });
    }
}