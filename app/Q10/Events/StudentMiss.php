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

class StudentMiss
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The evaluation instance.
     *
     * @var \App\Q10\Models\Evaluacion
     */
    public $evaluation;

    /**
     * The student instance.
     *
     * @var \App\Q10\Models\Estudiante
     */
    public $student;

    /**
     * The tag to add to the student.
     */
    public $tag;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Evaluacion $evaluation, Estudiante $student)
    {
        $this->student = $student;
        $this->evaluation = $evaluation;
        if ($this->evaluation->Cantidad_inasistencia == 1) {
            $this->tag = 'inasistencia-1';
        } elseif ($this->evaluation->Cantidad_inasistencia == 3){
            $this->tag = 'inasistencias-3';
        } elseif ($this->evaluation->Cantidad_inasistencia >= 5){
            $this->tag = 'inasistencias-5';
        }
    }
}
