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

class ThinkificEnrollQ10Student extends Command
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
        $student = $this->argument('student');
        if (is_int($student)) {
            $student = Student::find($student);
        }
        $course = $this->argument('tk_course');
        if (is_int($course)) {
            $course = Tkcourse::find($student);
        }

        $users = $client->get_paginated('users', [
            'query' => ['query[email]'=>$student->Email]
        ]);

        if($users->count() == 0){
            $response = $client->post('users', [
                'json' => new StudentTKResource($student)
            ]);
            $user = json_decode($response->getBody());
            Log::info('New user created on thinkific platform', ['User'=>$user->email]);
            TKStudentCreated::dispatch($student);

            $default_course = Tkcourse::where('default', true)->first();
            if(!is_null($default_course)){
                $client->enroll_user($user, $default_course);
            }
        } else {
            $user = $users->first();
        }

        $client->enroll_user($user, $course);
        TKStudentCourseEnrolled::dispatch($student, $course);
        Log::info('User enrolled in course', ['user'=>$user->id, 'course'=>$course->id]);
        return 0;
    }
}
