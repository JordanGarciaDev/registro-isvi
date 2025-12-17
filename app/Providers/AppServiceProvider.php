<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Gate::define('manage-system', function ($user) {
            return $user->hasRole('Administrador');
        });

        // Rol Turnos
        Gate::define('omt', function ($user) {
            return $user->hasRole('OMT');
        });

        // Rol Turnos
        Gate::define('jefeoperacion', function ($user) {
            return $user->hasRole('Jefe Operacion') || $user->hasRole('Jefe_operacion');
        });
    }
}
