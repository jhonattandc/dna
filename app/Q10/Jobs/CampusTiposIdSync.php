<?php

namespace App\Q10\Jobs;

use App\Q10\Models\Campus;
use App\Q10\Services\Q10API;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CampusTiposIdSync implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The campus instance.
     *
     * @var \App\Q10\Models\Campus
     */
    protected $campus;

    /**
     * The offset for the API pagination.
     *
     * @var int
     */
    protected $offset;

    /**
     * The number of item to retrieve from the API.
     */
    protected $limit = 35;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     */
    public $timeout = 120;

    /**
     * Create a new job instance.
     *
     * @param  \App\Q10\Models\Campus  $campus
     * @param  int  $offset
     *
     * @return void
     */
    public function __construct(Campus $campus, $offset = 1)
    {
        $this->campus = $campus;
        $this->offset = $offset;
    }

    /**
     * Execute the job.
     *
     * @param  \App\Q10\Services\Q10API  $httpClient
     *
     * @return void
     */
    public function handle(Q10API $httpClient)
    {
        $response = $httpClient->get_page('tiposidentificacion', [
            'headers' => [
                'Api-Key' => $this->campus->Secreto,
            ],
            'query' => [
                'Offset' => $this->offset,
                'Limit' => $this->limit,
            ],
        ]);

        if (!$httpClient->check_end($response)) {
            CampusTiposIdSync::dispatch($this->campus, $this->offset+1);
        }
        $collection = $httpClient->get_collection($response);

        foreach ($collection as $object_json) {
            $this->campus->tipos_id()->updateOrCreate(
                ['Codigo' => $object_json['Codigo']],
                [
                    'Nombre' => $object_json['Nombre'],
                    'Abreviatura' => $object_json['Abreviatura'],
                    'Estado' => $object_json['Estado'],
                ]
            );
        }
    }
}
