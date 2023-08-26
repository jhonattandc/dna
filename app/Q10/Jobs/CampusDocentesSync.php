<?php

namespace App\Q10\Jobs;

use App\Q10\Models\Campus;
use App\Q10\Models\Docente;
use App\Q10\Models\Usuario;
use App\Q10\Services\Q10API;

use Illuminate\Bus\Queueable;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CampusDocentesSync implements ShouldQueue
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
     * Execute the job.
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

        $response = $httpClient->get_page('administrativos', [
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
                new CampusDocentesSync($this->campus, $this->offset+1)
            );
        }
        $collection = $httpClient->get_collection($response);

        foreach ($collection as $object_json) {
            $docente = Docente::updateOrCreate(
                [
                    'Codigo' => $object_json['Codigo']
                ],
                [
                    'Primer_nombre' => $object_json['Primer_nombre'],
                    'Segundo_nombre' => $object_json['Segundo_nombre'],
                    'Primer_apellido' => $object_json['Primer_apellido'],
                    'Segundo_apellido' => $object_json['Segundo_apellido'],
                    'Numero_identificacion' => $object_json['Numero_identificacion'],
                    'Genero' => $object_json['Genero'],
                    'Email' => $object_json['Email'],
                    'Telefono' => $object_json['Telefono'],
                    'Celular' => $object_json['Celular'],
                    'Fecha_nacimiento' => $object_json['Fecha_nacimiento'],
                    'Lugar_nacimiento' => $object_json['Lugar_nacimiento'],
                    'Lugar_residencia' => $object_json['Lugar_residencia'],
                    'Direccion' => $object_json['Direccion'],
                ]
            );

            // Associate the usuario for the docente.
            $usuario = Usuario::where('Codigo_persona', $object_json['Codigo'])->first();
            if ($usuario) {
                $docente->usuario()->associate($usuario);
                $docente->save();
            }

            // Associate the tipo indentificacion for the docente.
            $tipo_id = $this->campus->tipos_id()->where('Codigo', $object_json['Codigo_tipo_identificacion'])->first();
            if ($tipo_id) {
                $docente->tipo_id()->associate($tipo_id);
                $docente->save();
            }
        }
    }
}
