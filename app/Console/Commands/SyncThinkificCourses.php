<?php

namespace App\Console\Commands;

use App\Models\Tkcourse;
use App\Services\ThinkificAPI;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SyncThinkificCourses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:TKcourses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronizes the courses of Thinkific database';

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
        $courses = $client->get_paginated('courses');
        $courses = $courses->map(function($object_json){
            $object_json = json_decode(json_encode($object_json), true);
            try{
                $course = Tkcourse::where('id', $object_json['id'])->firstOrFail();
                $course->fill($object_json);
            } catch (ModelNotFoundException $e) {
                $course = new Tkcourse($object_json);
            }
            $course->save();
            return $course;
        });
        return 0;
    }
}
