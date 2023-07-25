<?php

namespace App\Q10\Jobs;

use App\Q10\Models\Campus;
use App\Q10\Services\Q10API;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CampusJornadaSedeSync implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
     * Get all sedesjornadas relationship instances from the API and sync them with the local database.
     *
     * @param  \App\Q10\Services\Q10API  $httpClient
     *
     * @return void
     */
    public function handle(Q10API $httpClient)
    {
        $response = $httpClient->get_page('sedesjornadas', [
            'headers' => [
                'Api-Key' => $this->campus->Secreto,
            ],
            'query' => [
                'Offset' => $this->offset,
                'Limit' => $this->limit,
            ],
        ]);

        if (!$httpClient->check_end($response)) {
            CampusJornadasSync::dispatch($this->campus, $this->offset+1);
        }
        $collection = $httpClient->get_collection($response);

        foreach ($collection as $object_json) {
            /**
             * Each object_json has the following structure:
             * 
             * {
             *    "Consecutivo": 1,
             *    "Codigo_sede": "001",
             *    "Nombre_sede": "Principal",
             *    "Codigo_jornada": "001",
             *    "Nombre_jornada": "Diurna",
             *    "Sede_jornada": "Principal - Diurna",
             *    "Estado": true
             * }
             * 
             * The following code queries the database for the sede and jornada instances and creates the relationship 
             * and update the columns Consecutivo and Estado in the pivot table.
             */

            $sede = $this->campus->sedes()->where('Codigo', $object_json['Codigo_sede'])->first();
            $jornada = $this->campus->jornadas()->where('Codigo', $object_json['Codigo_jornada'])->first();
            if (!$sede || !$jornada) {
                continue;
            }

            $sede->jornadas()->syncWithoutDetaching([$jornada->id => [
                'Consecutivo' => $object_json['Consecutivo'],
                'Estado' => $object_json['Estado'],
            ]]);
        }
    }
}
