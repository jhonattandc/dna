<?php

namespace App\Listeners;

use App\Mail\TKUserCreated;
use App\Events\TKStudentCreated;

use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendEmailNotification
{
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
     * @param  \App\Events\TKStudentCreated  $event
     * @return void
     */
    public function handle(TKStudentCreated $event)
    {
        Mail::to($event->student->Email)->send(new TKUserCreated($event->student));
    }
}
