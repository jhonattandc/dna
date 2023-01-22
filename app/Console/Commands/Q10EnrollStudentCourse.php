<?php

namespace App\Console\Commands;

use App\Events\Q10StudentCourseEnrolled;
use App\Models\Course;
use App\Models\Student;

use Illuminate\Console\Command;

class Q10EnrollStudentCourse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'q10:enrollStudentCourse {student} {course}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Asociate a Q10 student with a Q10 program';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $student = $this->argument('student');
        if (is_int($student) || is_string($student)){
            $student = Student::find($student);
        }
        $course = $this->argument('course');
        if (is_int($course) || is_string($student)){
            $course = Course::find($course);
        }
        if (! $student->courses()->where('courses.id',$course->id)->exists()){
            $student->courses()->attach($course);
            Q10StudentCourseEnrolled::dispatch($student, $course);
        }
        return 0;
    }
}
