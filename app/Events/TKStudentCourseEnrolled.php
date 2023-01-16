<?php

namespace App\Events;

use App\Models\Student;
use App\Models\Tkcourse;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TKStudentCourseEnrolled
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The student instance.
     *
     * @var \App\Models\Student
     */
    public $student;

    /**
     * The student instance.
     *
     * @var \App\Models\Tkcourse
     */
    public $tkcourse;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Student $student, Tkcourse $tkcourse)
    {
        $this->student = $student;
        $this->tkcourse = $tkcourse;
    }
}
