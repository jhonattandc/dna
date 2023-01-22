<?php

namespace App\Listeners;

use App\Events\TKStudentCreated;

use App\Mail\NuevoUsuarioCreado;

use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class Q10StudentventSubscriber
{
    /**
     * Handle user login events.
     *
     * @param  \App\Events\TKStudentCreated  $event
     * @return void
     */
    public function handleThinkificStudentCreated(TKStudentCreated $event) {
        if (!is_null($event->student->Email)){
            Mail::to($event->student->Email)->send(new NuevoUsuarioCreado($event->student));
        }
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     * @return array
     */
    public function subscribe($events)
    {
        return [
            TKStudentCreated::class => 'handleThinkificStudentCreated',
        ];
    }
}
