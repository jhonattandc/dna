<?php

namespace App\Q10\Events;

use App\Q10\Models\Curso;
use App\Q10\Models\Estudiante;

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
     * @var \App\Q10\Models\Estudiante
     */
    public $student;

    /**
     * The student instance.
     *
     * @var \App\Q10\Models\Curso
     */
    public $course;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Estudiante $student, Curso $course)
    {
        $this->student = $student;
        $this->course = $course;
    }
}
