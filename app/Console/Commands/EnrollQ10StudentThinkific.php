<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Models\Tkcourse;
use App\Services\ThinkificAPI;
use App\Http\Resources\StudentTKResource;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class EnrollQ10StudentThinkific extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'thinkific:enrollQ10student {student} {tk_course}';

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
        $student = Student::find($this->argument('student'));
        $course = Tkcourse::find($this->argument('tk_course'));

        $users = $client->get_paginated('users', [
            'query' => ['query[email]'=>$student->Email]
        ]);

        if($users->count() == 0){
            $response = $client->post('users', [
                'json' => new StudentTKResource($student)
            ]);
            $user = json_decode($response->getBody());
            Log::debug('New user created on thinkific platform', ['User'=>$user->email]);

            $default_course = Tkcourse::where('default', true)->first();
            if(!is_null($default_course)){
                $client->enroll_user($user, $default_course);
            }
        } else {
            $user = $users->first();
            Log::debug('user id to delete', ['id'=>$user->id]);
        }

        $client->enroll_user($user, $course);
        return 0;
    }
}
