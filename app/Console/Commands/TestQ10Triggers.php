<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Student;
use App\Models\Evaluation;

use App\Events\Q10StudentMiss;
use App\Events\Q10StudentFailed;
use App\Events\Q10StudentPassed;

class TestQ10Triggers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:Q10triggers {student_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test triggers in Q10 database for evaluations';


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
        $student_id = $this->argument('student_id');
        $student = Student::find($student_id);
        $evaluation = Evaluation::factory()->make();
        $this->debug($student, $evaluation);
    }
}
