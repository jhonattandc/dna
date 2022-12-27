<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Models\Campus;
use App\Services\Q10API;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CoursesQ10 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:coursesQ10 {campus=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronizes the courses Q10 database';

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
        $this->info('Obteniendo los cursos');

        $campus_id = $this->argument('campus');
        $campus = Campus::find($campus_id);

        $client = new Q10API('/cursos', $campus->Secreto);
        $response = $client->get_paginated();

        $courses = $response->map(function ($object_json) use ($campus){
            $term = $campus->terms()->where('Consecutivo', $object_json['Consecutivo_periodo'])->first();
            if (is_null($term)) {
                $this->warn('No se encontró el periodo ' . $object_json['Consecutivo_periodo']);
                Log::warning('No se encontró el periodo', ['term'=>$object_json['Consecutivo_periodo']]);
                return;
            }
            try{
                $course = $term->courses()->where('Consecutivo', $object_json['Consecutivo'])->firstOrFail();
                $course->fill($object_json);
            } catch (ModelNotFoundException $e) {
                $course = new Course($object_json);
                $course->term_id = $term->id;
            }
            $course->save();
            return $course;
        });

        return 0;
    }
}
