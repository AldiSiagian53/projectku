<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AlertController;
use Carbon\Carbon;

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
        // Set default timezone untuk Carbon ke WIB (Asia/Jakarta)
        Carbon::setLocale('id');
        date_default_timezone_set(config('app.timezone', 'Asia/Jakarta'));
        
        // Share system status + alert count ke sidebar
        View::composer('components.sidebar', function ($view) {
            $systemStatus = DashboardController::getSystemStatus();
            $alertCounts = AlertController::getSidebarAlertCounts();

            $view->with('systemStatus', $systemStatus);
            $view->with('alertCounts', $alertCounts);
        });
    }
}
