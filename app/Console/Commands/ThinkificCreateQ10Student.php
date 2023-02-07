<?php

namespace App\Console\Commands;

use Exception;

use App\Models\Student;
use App\Services\ThinkificAPI;
use App\Http\Resources\StudentTKResource;

use App\Events\TKStudentCreated;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ThinkificCreateQ10Student extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'thinkific:createQ10student {student}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Q10 student user account on thinkific';

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
     * @return int
     */
    public function handle(ThinkificAPI $client)
    {
        try {
            $student = $this->argument('student');
            if (is_int($student) || is_string($student)) {
                $student = Student::find($student);
            }

            if(!$student){
                $this->error("El estudiante no existe");
                Log::error("El estudiante no existe");
                return 0;
            }
            if ($student->thinkific_id) {
                $this->error("El estudiante ya tiene una cuenta en thinkific");
                Log::error("El estudiante ya tiene una cuenta en thinkific");
                return 0;
            }
            if ($student->Email == null) {
                $this->error("El estudiante no tiene un correo electronico");
                Log::error("El estudiante no tiene un correo electronico");
                return 0;
            }
            if ($student->Primer_nombre == null) {
                $this->error("El estudiante no tiene un nombre");
                Log::error("El estudiante no tiene un nombre");
                return 0;
            }
            if ($student->Primer_apellido == null) {
                $this->error("El estudiante no tiene un apellido");
                Log::error("El estudiante no tiene un apellido");
                return 0;
            }
            if (!filter_var($student->Email, FILTER_VALIDATE_EMAIL)) {
                $this->error("El correo electronico del estudiante no es valido");
                Log::error("El correo electronico del estudiante no es valido");
                return 0;
            }

            $users = $client->get_paginated('users', [
                'query' => ['query[email]'=>$student->Email]
            ]);

            if($users->count() == 0){
                $response = $client->post('users', [
                    'json' => new StudentTKResource($student)
                ]);
                if($response->getStatusCode() >= 200 && $response->getStatusCode() < 300){
                    TKStudentCreated::dispatch($student);
                    Log::debug('New user created on thinkific platform', ['User'=>$student->Email]);
                    return json_decode($response->getBody())->id;
                }
            }else {
                return $users->first()->id;
            }
        } catch (Exception $e) {
            $this->error("Ocurrio un error creando un usuario en thinkific, revisar el log");
            Log::error("Ocurrio un error creando un usuario en thinkific", ["exception"=>$e->getMessage()]);
        }
        return 0;
    }
}
