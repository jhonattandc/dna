<?php

namespace App\Console\Commands;

use App\Models\Tkcourse;
use App\Services\ThinkificAPI;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

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
        Log::info("Sincronizando cursos con Thinkific");
        try {
            DB::disableQueryLog();
            $courses = $client->get_paginated('courses');
            # Status bar
            $bar = $this->output->createProgressBar(count($courses));
            $bar->start();

            $courses = $courses->map(function($object_json) use ($bar) {
                $object_json = json_decode(json_encode($object_json), true);
                try{
                    $course = Tkcourse::where('id', $object_json['id'])->firstOrFail();
                    $course->fill($object_json);
                } catch (ModelNotFoundException $e) {
                    $course = new Tkcourse($object_json);
                }
                $course->save();
                $bar->advance();
                return $course;
            });

            $bar->finish();
            $this->info(" ¡Cursos sincronizados!");
        } catch (\Throwable $th) {
            $this->error("Ocurrio un error creando un usuario en thinkificv, revisar el log");
            Log::error("Error creating an User in thinkific", ["exception"=>$th]);
        }
        Log::info("Sincronización de cursos con Thinkific finalizada");
        return 0;
    }
}
