<?php

namespace App\Thinkific\Jobs;

use App\Thinkific\Models\Student;
use App\Thinkific\Events\StudentCreated;
use App\Thinkific\Services\ThinkificAPI;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateStudent implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The student data.
     * 
     */
    protected $student;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($student)
    {
        $this->student = $student;
    }

    /**
     * Generate random password
     * 
     * @return string
     */
    public function generatePassword()
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $password = substr(str_shuffle($chars), 0, 8);
        return $password;
    }

    /**
     * Format the student data to be sent to Thinkific
     * 
     * @return array
     */
    public function formatStudentData($student)
    {
        return [
            'first_name' => ucwords(strtolower($student->Primer_nombre)),
            'last_name' => ucwords(strtolower($student->Primer_apellido)),
            'email' => $student->Email,
            'password' =>  $this->generatePassword(),
        ];
    }

    /**
     * Execute the job.
     * 
     * @param  \App\Thinkific\Services\ThinkificAPI
     *
     * @return void
     */
    public function handle(ThinkificAPI $client)
    {
        if ($this->batch()->cancelled()) {
            return;
        }

        if (Student::where('email', $this->student->Email)->exists()) {
            return;
        }

        $thinkific_users = $client->get_paginated('users', [
            'query' => ['query[email]'=>$this->student->Email]
        ]);
        
        $thinkific_user = new Student();
        if (count($thinkific_users) == 0) {
            $data_format = $this->formatStudentData($this->student);
            $object_json = $client->create_user($data_format);
            if ($object_json instanceof Exception) {
                $this->fail($object_json);
            }
            $thinkific_user->random_password = $data_format['password'];
        } else {
            $object_json = $thinkific_users[0];
        }
        
        $thinkific_user->email = $this->student->Email;
        $thinkific_user->thinkific_id = $object_json->id;
        $thinkific_user->estudiante()->associate($this->student);
        $thinkific_user->save();
        event(new StudentCreated($thinkific_user));
    }
}
