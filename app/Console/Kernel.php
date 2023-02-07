<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('sync:Q10')->daily()->withoutOverlapping();
        $schedule->command('sync:TKcourse')->daily()->withoutOverlapping();
        $schedule->command('sync:Q10evaluations')->everyFourHours()->withoutOverlapping();
        $schedule->command('sync:TKstudents')->everyFifteenMinutes()->withoutOverlapping();
        $schedule->command('sync:Q10Students')->everyThreeHours()->withoutOverlapping();
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
