<?php

namespace App\Mail;

use App\Models\Student;
use App\Models\Evaluation;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FiveMissClass extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The student instance.
     *
     * @var \App\Models\Student
     */
    public $student;

    /**
     * The evaluation instance.
     *
     * @var \App\Models\Evaluation
     */
    public $evaluation;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Student $student, Evaluation $evaluation)
    {
        $this->student = $student;
        $this->evaluation = $evaluation;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if(is_null($this->evaluation->Nombre_asignatura)){
            $name = $this->evaluation->Nombre_curso;
        } else {
            $name = $this->evaluation->Nombre_asignatura;
        }
        return $this->subject(ucfirst(mb_strtolower($this->student->Primer_nombre, 'UTF-8')).', reprobaste tu curso')->
            view('emails.q10.fiveMissClass',
                ['student'=>$this->student, 'course_name'=>$name]
            );
    }
}
