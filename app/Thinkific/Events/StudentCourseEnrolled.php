<?php

namespace App\Thinkific\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudentCourseEnrolled
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The student instance.
     *
     */
    public $student;

    /**
     * The course instance.
     */
    public $course;

    /**
     * Create a new event instance.
     *
     *
     * @return void
     */
    public function __construct($student, $course)
    {
        $this->student = $student;
        $this->course = $course;
    }
}
