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
        /*---------------------------------------------------------------------------------
        | Horizon Snapshot
        |----------------------------------------------------------------------------------
        */
        $schedule->command('horizon:snapshot')->everyFiveMinutes();

        /*---------------------------------------------------------------------------------
        | Q10 Sync
        |----------------------------------------------------------------------------------
        */
        $schedule->command('sync:preUsersQ10')->everyFiveMinutes();
        $schedule->command('sync:preAcademicQ10')->everyTenMinutes();
        $schedule->command('sync:coursesQ10')->everyFifteenMinutes();
        $schedule->command('sync:staffQ10')->everyThirtyMinutes();
        $schedule->command('sync:usersQ10')->hourly();
        $schedule->command('sync:evaluationsQ10')->daily();

        /*---------------------------------------------------------------------------------
        | Thinkific Sync
        |----------------------------------------------------------------------------------
        */
        $schedule->command('sync:studentsTK')->everyFiveMinutes()->withoutOverlapping();
        $schedule->command('sync:coursesTK')->hourly()->withoutOverlapping();
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
        $this->load(__DIR__.'/../Thinkific/Console/Commands');
        $this->load(__DIR__.'/../Prosegur/Console/Commands');

        require base_path('routes/console.php');
    }
}
