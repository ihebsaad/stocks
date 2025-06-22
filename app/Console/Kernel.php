<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        
        \App\Console\Commands\SyncParcelStatuses::class,

    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        //$schedule->command('parcels:sync-statuses')->daily(); // ou ->everyThirtyMinutes(), etc.
        $schedule->command('parcels:sync-statuses')->everyTwoHours(); // ou ->everyThirtyMinutes(), etc.

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
//* * * * * cd /chemin/vers/ton-projet && php artisan schedule:run >> /dev/null 2>&1
//php artisan parcels:sync-statuses
