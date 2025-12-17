<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\DashboardController;

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
        // Share system status data ke semua view (untuk sidebar)
        View::composer('components.sidebar', function ($view) {
            $systemStatus = DashboardController::getSystemStatus();
            $view->with('systemStatus', $systemStatus);
        });
    }
}
