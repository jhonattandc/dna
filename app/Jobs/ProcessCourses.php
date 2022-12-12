<?php

namespace App\Jobs;

use App\Models\Campus;
use App\Models\Course;
use App\Services\Q10API;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProcessCourses implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
     * @return void
     */
    public function __construct(Campus $campus)
    {
        $this->campus = $campus;
        $this->client = new Q10API('/cursos', $campus->Secreto);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        logger('Getting courses records from Q10', ['Sede' => $this->campus->Nombre]);
        $campus = $this->campus;
        $response = $this->client->get_paginated();
        $courses = $response->map(function ($object_json) use ($campus){
            $term = $campus->terms()->where('Consecutivo', $object_json['Consecutivo_periodo'])->firstOrFail();
            try{
                $course = $term->courses()->where('Consecutivo', $object_json['Consecutivo'])->firstOrFail();
            } catch (ModelNotFoundException $e) {
                $course = new Course($object_json);
                $course->term_id = $term->id;
            }
            $course->save();
            ProcessEvaluations::dispatch($campus, $course)->onQueue($this->campus->Cola);
            return $course;
        });
    }
}
