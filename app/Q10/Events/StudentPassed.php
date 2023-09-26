<?php

namespace App\Q10\Events;

use App\Q10\Models\Estudiante;
use App\Q10\Models\Evaluacion;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudentPassed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The student instance.
     *
     * @var \App\Q10\Models\Estudiante
     */
    public $student;

    /**
     * The evaluation instance.
     *
     * @var \App\Q10\Models\Evaluacion
     */
    public $evaluation;

    /**
     * The tag to add to the student.
     */
    public $tag = 'materia-aprobada';

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Evaluacion $evaluation, Estudiante $student)
    {
        $this->evaluation = $evaluation;
        $this->student = $student;
    }
}
