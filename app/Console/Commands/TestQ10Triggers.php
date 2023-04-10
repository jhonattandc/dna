<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Student;

use App\Services\ClientifyAPI;

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
        // $evaluation = Evaluation::factory()->create([
        //     'student_id' => $student->id,
        //     'course_id' => $student->courses->first()->id,
        //     'subject_id' => $student->subjects->first()->id,
        //     'score' => 0,
        // ]);

        $clientify = new ClientifyAPI();
        $contacts = $clientify->queryContact($student);
        $this->info($contacts->count());
        if($contacts->count() > 0){
            $contact = $contacts->first();
            $this->info($contact->id);
            $clientify->addTagToContact($contact->id, 'newest');
            $this->info('newest');
            sleep(5);
            $clientify->addTagToContact($contact->id, 'materia-aprobada');
            $this->info('materia-aprobada');
            sleep(5);
            $clientify->addTagToContact($contact->id, 'materia-reprobada');
            $this->info('materia-reprobada');
            sleep(5);
            $clientify->addTagToContact($contact->id, 'inasistencia-1');
            $this->info('inasisitencia-1');
            sleep(5);
            $clientify->addTagToContact($contact->id, 'inasistencias-3');
            $this->info('inasisitencia-3');
            sleep(5);
            $clientify->addTagToContact($contact->id, 'inasistencias-5');
            $this->info('inasisitencia-5');
        }
    }
}
