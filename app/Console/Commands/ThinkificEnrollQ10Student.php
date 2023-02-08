<?php

namespace App\Console\Commands;

use Exception;

use App\Models\Student;

use App\Services\ThinkificAPI;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ThinkificEnrollQ10Student extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'thinkific:enrollQ10student {student} {tkcourse_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enroll a Q10 student user account on a thinkific course';

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
     * Get the student from the argument
     *
     * @return App\Models\Student
     */
    public function getStudent()
    {
        $student = $this->argument('student');
        if (is_int($student) || is_string($student)) {
            $student = Student::find($student);
        }
        if(!$student){
            $this->error("El estudiante no existe");
            Log::error("El estudiante no existe");
            return null;
        }
        if ($student->Email == null) {
            $this->error("El estudiante no tiene un correo electronico");
            Log::error("El estudiante no tiene un correo electronico");
            return null;
        }
        return $student;
    }

    /**
     * Execute the console command.
     *
     * @param  App\Services\ThinkificAPI
     * @return int
     */
    public function handle(ThinkificAPI $client)
    {
        try {
            $student = $this->getStudent();
            if (!$student) {
                return 1;
            }
            $tkcourse_id = $this->argument('tkcourse_id');
            $tkuser_id = $student->tk_id;

            $enroll = $client->get_paginated('enrollments', [
                'query' => [
                    'query[user_id]' => $tkuser_id,
                    'query[course_id]' => $tkcourse_id
                ]
            ]);

            if ($enroll->count() == 0) {
                $enrollments = $client->post('enrollments', [
                    'json' => [
                        'course_id'=>$tkcourse_id,
                        'user_id'=> $tkuser_id,
                        'activated_at'=>date('c')
                    ]
                ]);
                $enrollment = json_decode($enrollments->getBody(), true);
                Log::debug('User matriculated in course', ['enrollment'=>$enrollment]);
                return 0;
            }
            return 1;
        } catch (Exception $e) {
           $this->error("Ocurrio un error creando un usuario en thinkific, revisar el log");
           Log::error("Ocurrio un error creando un usuario en thinkific", ["exception"=>$e->getMessage()]);
            return 1;
        }
    }
}
