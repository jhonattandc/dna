<?php

namespace App\Console\Commands;

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

            $users = $client->get_paginated('users', [
                'query' => ['query[email]'=>$student->Email]
            ]);

            if($users->count() == 0){
                $response = $client->post('users', [
                    'json' => new StudentTKResource($student)
                ]);
                if($response->getStatusCode() > 200 && $response->getStatusCode() < 300){
                    TKStudentCreated::dispatch($student);
                    Log::debug('New user created on thinkific platform', ['User'=>$student->Email]);
                    return json_decode($response->getBody())->id;
                }
            }else {
                return $users->first()->id;
            }
        } catch (\Throwable $th) {
            $this->error("Ocurrio un error creando un usuario en thinkificv, revisar el log");
            Log::error("Error creating an User in thinkific", ["exception"=>$th]);
        }
        return 0;
    }
}
