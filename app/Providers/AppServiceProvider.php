<?php

namespace App\Providers;

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
        // Note : la policy PatientAccessPolicy n'est PAS auto-découverte par Laravel
        // (car elle n'est pas liée à un modèle Eloquent par convention User::class).
        // On l'utilise donc directement via app(PatientAccessPolicy::class)->method()
        // dans DoctorController. Pas besoin d'enregistrement Gate ici pour le moment.
    }
}
