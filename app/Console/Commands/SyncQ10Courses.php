<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Models\Campus;
use App\Services\Q10API;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class SyncQ10Courses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:Q10courses {campus}';

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
        DB::disableQueryLog();
        $this->info('Obteniendo los cursos');

        $campus = $this->argument('campus');
        if (is_int($campus) || is_string($campus)){
            $campus = Campus::find($campus);
        }

        $client = new Q10API('/cursos', $campus->Secreto);
        $response = $client->get_paginated();

        $courses = $response->map(function ($object_json) use ($campus){
            $term = $campus->terms()->where('Consecutivo', $object_json['Consecutivo_periodo'])->first();
            if (is_null($term)) {
                $this->warn('No se encontró el periodo con id '.$object_json['Consecutivo_periodo'].' mientras se procesaba el curso '.$object_json['Codigo']);
                Log::warning('No se encontró el periodo mientras se procesan los cursos', ['Sede'=>$campus->Nombre, 'Curso'=>$object_json['Codigo'], 'Periodo'=>$object_json['Consecutivo_periodo']]);
                return;
            }
            try{
                $course = $term->courses()->where('Consecutivo', $object_json['Consecutivo'])->firstOrFail();
                $course->fill($object_json);
            } catch (ModelNotFoundException $e) {
                $course = new Course($object_json);
                $course->term_id = $term->id;
                Log::warning('Nuevo curso de Q10 sincronizado, es necesario asignarle un curso de thinkific', ['curso'=>$course->id]);
            }
            $course->save();
            return $course;
        });

        return 0;
    }
}
