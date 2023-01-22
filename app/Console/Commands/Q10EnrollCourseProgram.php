<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Models\Program;

use Illuminate\Console\Command;

class Q10EnrollCourseProgram extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'q10:enrollCourseProgram {course} {program}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Asociate a Q10 course with a Q10 program';

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
        $course = $this->argument('course');
        if (is_int($course) || is_string($course)){
            $course = Course::find($course);
        }
        $program = $this->argument('program');
        if (is_int($program) || is_string($program)){
            $program = Program::find($program);
        }
        if (! $course->programs()->where('programs.id', $program->id)->exists()){
            $course->programs()->attach($program->id);
        }
        return 0;
    }
}
