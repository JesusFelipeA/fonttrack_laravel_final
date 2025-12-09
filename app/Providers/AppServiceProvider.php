<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Buscador;
use App\Services\Search\NombreSearchStrategy;
use App\Services\Search\CodigoSearchStrategy;
use App\Services\Search\FechaSearchStrategy;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('public.path', function() {
            return base_path().'/public_html';
        });

        $this->app->singleton(\App\Services\DashboardManager::class, function ($app) {
            return new \App\Services\DashboardManager();
        });
        $this->app->singleton(Buscador::class, function ($app) {
            return new Buscador([
                'nombre' => new NombreSearchStrategy(),
                'codigo' => new CodigoSearchStrategy(),
                'fecha' => new FechaSearchStrategy(),
            ]);
        });

        // Registrar DAOs
        $this->app->bind(
            \App\DAO\Interfaces\MaterialDAOInterface::class,
            \App\DAO\Implementations\MaterialDAO::class
        );

        // Registrar Repositories
        $this->app->singleton(\App\Repositories\MaterialRepository::class);

        // Registrar Services
        $this->app->singleton(\App\Services\MaterialService::class);

        // Registrar ViewModels
        $this->app->bind(\App\ViewModels\MaterialViewModel::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
