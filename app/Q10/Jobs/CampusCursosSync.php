<?php

namespace App\Q10\Jobs;

use App\Q10\Models\Campus;
use App\Q10\Models\Curso;
use App\Q10\Services\Q10API;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CampusCursosSync implements ShouldQueue
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
     * Get all role instances from the API and sync them with the local database.
     *
     * @param  \App\Q10\Services\Q10API  $httpClient
     *
     * @return void
     */
    public function handle(Q10API $httpClient)
    {
        if ($this->batch()->cancelled()) {
            return;
        }
        // Update or create the role instances.
        $response = $httpClient->get_page('cursos', [
            'headers' => [
                'Api-Key' => $this->campus->Secreto,
            ],
            'query' => [
                'Offset' => $this->offset,
                'Limit' => $this->limit,
            ],
        ]);

        if ($response->getStatusCode() != 200) {
            $this->fail();
        }

        if (!$httpClient->check_end($response)) {
            $this->batch()->add(
                new CampusCursosSync($this->campus, $this->offset+1)
            );
        }
        $collection = $httpClient->get_collection($response);

        foreach ($collection as $object_json) {
            $curso = Curso::updateOrCreate(
                [
                    'Consecutivo' => $object_json['Consecutivo'],
                    'Codigo' => $object_json['Codigo']
                ],
                [
                    'Nombre' => $object_json['Nombre'],
                    'Cupo_maximo' => $object_json['Cupo_maximo'],
                    'Fecha_inicio' => $object_json['Fecha_inicio'],
                    'Fecha_fin' => $object_json['Fecha_fin'],
                    'Cantidad_estudiantes_matriculados' => $object_json['Cantidad_estudiantes_matriculados'],
                ]
            );

            $periodo = $this->campus->periodos()->where('Consecutivo', $object_json['Consecutivo_periodo'])->first();
            if($periodo) {
                $curso->periodo()->associate($periodo);
                $curso->save();
            }

            $programa = $this->campus->programas()->where('Codigo', $object_json['Codigo_programa'])->first();
            if($programa) {
                $curso->programa()->associate($programa);
                $curso->save();
            }

            $jornada_sede = $this->campus->jornada_sedes()->where('Consecutivo', $object_json['Consecutivo_sede_jornada'])->first();
            if ($jornada_sede) {
                $curso->jornada_sede()->associate($jornada_sede);
                $curso->save();
            }
        }
    }
}
