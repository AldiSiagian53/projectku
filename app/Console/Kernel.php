<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\RecordCondition;
use App\Console\Commands\CleanupConditionLogs;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Catat kondisi setiap 10 menit
        $schedule->command('condition:record')->everyTenMinutes();

        // Setiap hari menjelang tengah malam, bersihkan data di bawah jam 21:00
        $schedule->command('condition:cleanup')->dailyAt('23:55');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
    }
}


