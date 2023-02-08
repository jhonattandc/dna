<?php

namespace App\Listeners;

use App\Events\TKStudentCreated;
use App\Events\Q10StudentMiss;
use App\Events\Q10StudentPassed;
use App\Events\Q10StudentFailed;

use App\Mail\NewUserCreated;
use App\Mail\OneMissClass;
use App\Mail\TwoMissClass;
use App\Mail\FiveMissClass;
use App\Mail\PassedCourse;
use App\Mail\FailedCourse;

use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class Q10StudentventSubscriber
{
    /**
     * Handle new user created on thinkific events.
     *
     * @param  \App\Events\TKStudentCreated  $event
     * @return void
     */
    public function handleThinkificStudentCreated(TKStudentCreated $event) {
        if (!is_null($event->student->Email)){
            Mail::to($event->student->Email)->send(new NewUserCreated($event->student));
        }
    }

    /**
     * Handle missed class of Q10 students.
     *
     * @param  \App\Events\Q10StudentMiss  $event
     * @return void
     */
    public function handleQ10MissClass(Q10StudentMiss $event) {
        if (is_null($event->student->Email)){
            return;
        }
        $email = $event->student->Email;
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            return;
        }
        $cantidad_inasistencia = $event->evaluation->Cantidad_inasistencia;
        if ($cantidad_inasistencia == 1) {
            Mail::to($email)->send(new OneMissClass($event->student, $event->evaluation));
        } elseif ($cantidad_inasistencia == 2) {
            Mail::to($email)->send(new TwoMissClass($event->student, $event->evaluation));
        } elseif ($cantidad_inasistencia >= 5) {
            Mail::to($email)->send(new FiveMissClass($event->student, $event->evaluation));
        }
    }

    /**
     * Handle passed course of Q10 students.
     *
     * @param  \App\Events\Q10StudentPassed  $event
     * @return void
     */
    public function handleQ10PassedCourse(Q10StudentPassed $event) {
        if (is_null($event->student->Email)){
            return;
        }
        $email = $event->student->Email;
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            return;
        }
        Mail::to($email)->send(new PassedCourse($event->student, $event->evaluation));
    }

    /**
     * Handle failed course of Q10 students.
     *
     * @param  \App\Events\Q10StudentFailed  $event
     * @return void
     */
    public function handleQ10FailedCourse(Q10StudentFailed $event) {
        if (is_null($event->student->Email)){
            return;
        }
        $email = $event->student->Email;
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            return;
        }
        Mail::to($email)->send(new FailedCourse($event->student, $event->evaluation));
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
            Q10StudentMiss::class => 'handleQ10MissClass',
            Q10StudentPassed::class => 'handleQ10PassedCourse',
            Q10StudentFailed::class => 'handleQ10FailedCourse',
        ];
    }
}
