<?php

namespace App\Providers;

use App\Models\Centros;
use App\Models\Clientes;
use App\Models\Grupos;
use App\Observers\BitacoraObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Centros::observe(BitacoraObserver::class); 
        Clientes::observe(BitacoraObserver::class);
        Grupos::observe(BitacoraObserver::class);
    }
}
