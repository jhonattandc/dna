<?php

namespace App\Jobs;

use App\Models\Campus;
use App\Models\Course;
use App\Services\Q10API;
use App\Models\Evaluation;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProcessEvaluations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The courses collection from a campus.
     *
     * @var \App\Models\Course
     */
    protected $course;

    /**
     * The campus instance.
     *
     * @var \App\Models\Campus
     */
    protected $campus;

    /**
     * The http client instance.
     *
     * @var \App\Services\Q10API
     */
    protected $client;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\Campus $campus
     *
     * @param \App\Models\Course $course
     *
     * @return void
     */
    public function __construct(Campus $campus, Course $course)
    {
        $this->campus = $campus;
        $this->course = $course;
        $this->client = new Q10API('/evaluaciones', $this->campus->Secreto);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $course = $this->course;
        $term = $course->term;
        if (!$term->Habilitado){
            return;
        }
        $programs = is_null($course->programs()->first()) ? $this->campus->programs : $course->programs;

        $collection = $programs->map(function($program) use ($term, $course){
            return [
                'Programa' => $program->Codigo,
                'Periodo' => $term->Consecutivo,
                'Curso' => $course->Consecutivo
            ];
        });

        // TODO: Convertir en concurrate

        // foreach ($this->campus->programs as $program) {
        //     $response = $this->client->get_paginated([
        //         'Programa' => $program->Codigo,
        //         'Periodo' => $term->Consecutivo,
        //         'Curso' => $course->Consecutivo
        //     ]);

        //     $evaluations = $response->map(function ($object_json) use ($course, $program) {
        //         try{
        //             $evaluation = $course->evaluations()->where('Codigo_estudiante', $object_json['Codigo_estudiante'])->firstOrFail();
        //             $evaluation->fill($object_json);
        //         } catch (ModelNotFoundException $e) {
        //             $evaluation = new Evaluation($object_json);
        //             $evaluation->course_id = $course->id;
        //             $course->programs()->attach($program->id);
        //         }
        //         $evaluation->save();
        //         logger('New evaluation saved', ['id'=>$evaluation->id]);
        //         return $evaluation;
        //     });
        // }
    }
}
