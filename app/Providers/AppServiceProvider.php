<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
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
        /* $this->loadMigrationsFrom([
            database_path('migrations/central')
        ]);
        // Si la conexión es la central, llama a los de central
        if (config('database.default') === 'central') {
            $this->call(\Database\Seeders\Central\DatabaseSeeder::class);
        } else {
            // Si no, llama a los de tenant
            $this->call(\Database\Seeders\Tenant\DatabaseSeeder::class);
        } */
       if(env('app.env') !== 'local') {
       URL::forceScheme('https');
       }
    }
}
