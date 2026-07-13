<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Contract;
use App\Observers\ContractObserver;
use Illuminate\Support\Facades\URL;

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
        // Vinculación de tu Observer existente
        Contract::observe(ContractObserver::class);

        // Fuerza el protocolo HTTPS en producción para evitar bloqueos de CSS/JS en Railway
        if (config('app.env') === 'production' || env('RAILWAY_ENVIRONMENT_NAME')) {
            URL::forceScheme('https');
        }
    }
}