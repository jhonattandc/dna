<?php

namespace App\Thinkific\Listeners;

use App\Thinkific\Models\Course;
use App\Thinkific\Events\StudentCreated;
use App\Thinkific\Jobs\EnrollStudent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class EnrollStudentInOnboarding
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
     * 
     * @return void
     */
    public function handle(StudentCreated $event)
    {
        // Get the default onboarding courses
        $courses = Course::where('default', true)->get();
        // Enroll the student in the onboarding courses
        $jobs = $courses->map(function($course) use ($event) {
            return new EnrollStudent($course->thinkific_id, $event->student->thinkific_id);
        });
        Bus::chain($jobs)->dispatch();
    }
}
