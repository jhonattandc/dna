<?php

namespace App\Events;

use App\Models\Student;
use App\Models\Evaluation;

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
     * The evaluation instance.
     *
     * @var \App\Models\Evaluation
     */
    public $evaluation;

    /**
     * The student instance.
     *
     * @var \App\Models\Student
     */
    public $student;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Evaluation $evaluation, Student $student)
    {
        $this->evaluation = $evaluation;
        $this->student = $student;
    }
}
