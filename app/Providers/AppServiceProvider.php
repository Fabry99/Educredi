<?php

namespace App\Providers;

use App\Models\Asesores;
use App\Models\Centros;
use App\Models\Centros_Grupos_Clientes;
use App\Models\Clientes;
use App\Models\Colector;
use App\Models\debeser;
use App\Models\Formapago;
use App\Models\Grupos;
use App\Models\saldoprestamo;
use App\Models\Sucursales;
use App\Models\Supervisores;
use App\Models\Tipopago;
use App\Models\User;
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
        saldoprestamo::observe(BitacoraObserver::class);
        // debeser::observe(BitacoraObserver::class);
        Centros_Grupos_Clientes::observe(BitacoraObserver::class);
        Asesores::observe(BitacoraObserver::class);
        Supervisores::observe(BitacoraObserver::class);
        User::observe(BitacoraObserver::class);
        Colector::observe(BitacoraObserver::class);
        Formapago::observe(BitacoraObserver::class);
        Sucursales::observe(BitacoraObserver::class);
        Supervisores::observe(BitacoraObserver::class);
        Tipopago::observe(BitacoraObserver::class);
    }
}
