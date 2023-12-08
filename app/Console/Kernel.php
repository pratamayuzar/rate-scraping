<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Services\ExchangeRateService;
use App\Repositories\ExchangeRateRepository;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();

        // Run scraping rates every 7mins
        $schedule->call(function () {
            $repo = new ExchangeRateRepository();
            $service = new ExchangeRateService($repo);
            $service->scrapeAndSave();
        })->cron('*/7 * * * *');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
