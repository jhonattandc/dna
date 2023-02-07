<?php

namespace App\Events;

use App\Models\Student;
use App\Models\Course;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Q10StudentPassed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The student instance.
     *
     * @var \App\Models\Student
     */
    public $student;

    /**
     * The course instance.
     *
     * @var \App\Models\Course
     */
    public $course;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Student $student, Course $course)
    {
        $this->student = $student;
        $this->course = $course;
    }
}
