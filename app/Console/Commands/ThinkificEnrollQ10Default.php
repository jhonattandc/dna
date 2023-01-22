<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Models\Tkcourse;
use App\Services\ThinkificAPI;
use App\Http\Resources\StudentTKResource;

use App\Events\TKStudentCreated;
use App\Events\TKStudentCourseEnrolled;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ThinkificEnrollQ10Default extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'thinkific:enrollQ10default {student}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enroll Q10 student in thinkific course';

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
     * @param  App\Services\ThinkificAPI
     * @return mixed
     */
    public function handle(ThinkificAPI $client)
    {
        try {
            $student = $this->argument('student');
            if (is_int($student) || is_string($student)) {
                $student = Student::find($student);
            }

            # Se verifica que el usuario tenga una cuenta en thinkific con el correo asociado a Q10, de lo contrario
            # se le crea una cuenta con sus datos por defecto
            if(is_null($student->tk_id)){
                $student_tk_id = $this->call('thinkific:createQ10student', ['student'=>$student]);
                if($student_tk_id == 0) {
                    return false;
                }
                $student->tk_id = $student_tk_id;
                $student->save();
            }

            # Se inscribe al usuario en el curso de onboarding
            $default_course = Tkcourse::where('default', true)->first();
            if(is_null($default_course)){
                return false;
            }
            $client->enroll_user($student->tk_id, $default_course);
            return true;
        } catch (\Throwable $th) {
            $this->error("Ocurrio un error creando un usuario en thinkificv, revisar el log");
            Log::error("Error creating an User in thinkific", ["exception"=>$th]);
            return false;
        }

    }
}
