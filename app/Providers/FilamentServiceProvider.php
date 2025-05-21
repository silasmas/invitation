<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Filament::serving(function () {
            Filament::registerUserMenuItems([
                // Tu peux personnaliser ici selon les rôles
            ]);
        });
        // Filament::registerWidgets([
        //     StatsOverview::class => fn () => auth()->user()?->can('view_dashboard'),
        // ]);
        
    }
}
