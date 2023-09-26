<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendEmailNotification
{
    /**
     * Map tags and emails to send.
     */
    protected $emails = [
        'newest' => 'NewUserCreated',
        'inasistencia-1' => 'OneMissClass',
        'inasistencias-3' => 'TwoMissClass',
        'inasistencias-5' => 'FiveMissClass',
        'materia-reprobada' => 'FailedCourse',
        'materia-aprobada' => 'PassedCourse',
    ];

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        //
    }
}
