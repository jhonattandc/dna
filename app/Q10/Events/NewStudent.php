<?php

namespace App\Q10\Events;

use App\Q10\Models\Estudiante;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


class NewStudent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The student instance.
     *
     * @var \App\Q10\Models\Estudiante
     */
    public $student;

    /**
     * The tag to add to the student.
     */
    public $tag = 'newest';

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Estudiante $student)
    {
        $this->student = $student;
    }
}
