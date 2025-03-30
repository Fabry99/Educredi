<?php

namespace App\Providers;

use App\Models\Centros;
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
    }
}
