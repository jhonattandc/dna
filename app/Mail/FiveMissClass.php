<?php

namespace App\Mail;

use App\Models\Student;
use App\Models\Course;

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
     * The course instance.
     *
     * @var \App\Models\Course
     */
    public $course;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Student $student, Course $course)
    {
        $this->student = $student;
        $this->course = $course;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(', reprobaste tu curso')->
            view('emails.q10.fiveMissClass',
                ['student'=>$this->student, 'course'=>$this->course]
            );
    }
}
