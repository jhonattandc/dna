<?php

namespace App\Events;

use App\Models\Student;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TKStudentCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The student instance.
     *
     * @var \App\Models\Student
     */
    public $student;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Student $student
     *
     * @return void
     */
    public function __construct(Student $student)
    {
        $this->student = $student;
    }
}