<?php

namespace App\Jobs;

use App\Models\Campus;
use App\Models\Term;
use App\Services\Q10API;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProcessTerms implements ShouldQueue
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
        $this->client = new Q10API('/periodos', $campus->Secreto);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        logger('Getting terms records from Q10', ['Sede' => $this->campus->Nombre]);
        $response = $this->client->get_paginated();
        foreach($response as $object_json) {
            try{
                $model = $this->campus->terms()->where('Consecutivo', $object_json['Consecutivo'])->firstOrFail();
            } catch (ModelNotFoundException $e) {
                $term = new Term($object_json);
                $term->campus_id = $this->campus->id;
                $term->save();
            }
        }
    }
}
