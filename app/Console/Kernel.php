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
        \Webklex\IMAP\Commands\ImapIdleCommand::class,
    ];
    
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('sync:Q10')->daily()->withoutOverlapping();
        // $schedule->command('sync:TKcourse')->daily()->withoutOverlapping();
        // $schedule->command('sync:Q10evaluations')->everyThirtyMinutes()->withoutOverlapping();
        // $schedule->command('sync:TKstudents')->everyTenMinutes()->withoutOverlapping();
        // $schedule->command('sync:Q10Students')->hourly()->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        $this->load(__DIR__.'/../Q10/Console/Commands');
        $this->load(__DIR__.'/../Prosegur/Console/Commands');

        require base_path('routes/console.php');
    }
}
