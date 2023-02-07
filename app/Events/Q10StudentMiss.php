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

class Q10StudentMiss
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
     * The missed number.
     *
     * @var int
     */
    public $missed_number;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Student $student, Course $course, $missed_number)
    {
        $this->student = $student;
        $this->course = $course;
        $this->missed_number = $missed_number;
    }
}
