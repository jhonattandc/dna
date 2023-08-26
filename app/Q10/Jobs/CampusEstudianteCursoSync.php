<?php

namespace App\Q10\Jobs;

use App\Q10\Models\Campus;
use App\Q10\Models\Curso;
use App\Q10\Models\Estudiante;
use App\Q10\Models\Usuario;

use App\Q10\Events\NewStudent;
use App\Q10\Services\Q10API;

use Illuminate\Bus\Queueable;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CampusEstudianteCursoSync implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The campus instance.
     *
     * @var \App\Q10\Models\Campus
     */
    protected $campus;

    /**
     * The curso instance.
     *
     * @var \App\Q10\Models\Curso
     */
    protected $curso;
    
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
     * @param  \App\Q10\Models\Curso  $curso
     * @param  int  $offset
     * 
     * @return void
     */
    public function __construct(Campus $campus, Curso $curso, $offset = 1)
    {
        $this->campus = $campus;
        $this->curso = $curso;
        $this->offset = $offset;
    }

    private function fill_student(Estudiante $estudiante, array $object_json)
    {
        $estudiante->Primer_nombre = $object_json['Primer_nombre'];
        $estudiante->Segundo_nombre = $object_json['Segundo_nombre'];
        $estudiante->Primer_apellido = $object_json['Primer_apellido'];
        $estudiante->Segundo_apellido = $object_json['Segundo_apellido'];
        $estudiante->Numero_identificacion = $object_json['Numero_identificacion'];
        $estudiante->Genero = $object_json['Genero'];
        $estudiante->Email = $object_json['Email'];
        $estudiante->Telefono = $object_json['Telefono'];
        $estudiante->Celular = $object_json['Celular'];
        $estudiante->Fecha_nacimiento = $object_json['Fecha_nacimiento'];
        $estudiante->Lugar_nacimiento = $object_json['Lugar_nacimiento'];
        $estudiante->Lugar_residencia = $object_json['Lugar_residencia'];
        $estudiante->Direccion = $object_json['Direccion'];
        $estudiante->save();
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

        $response = $httpClient->get_page('estudiantes', [
            'headers' => [
                'Api-Key' => $this->campus->Secreto,
            ],
            'query' => [
                'Periodo' => $this->curso->periodo->Consecutivo,
                'Sede_jornada' => $this->curso->jornada_sede->Consecutivo,
                'Programa' => $this->curso->programa->Codigo,
                'Curso' => $this->curso->Consecutivo,
                'Offset' => $this->offset,
                'Limit' => $this->limit,
            ],
        ]);

        if ($response->getStatusCode() != 200) {
            $this->fail();
        }

        if (!$httpClient->check_end($response)) {
            $this->batch()->add(
                new CampusEstudianteCursoSync($this->campus, $this->curso, $this->offset+1)
            );
        }
        $collection = $httpClient->get_collection($response);

        foreach ($collection as $object_json) {
            if (Estudiante::where('Codigo', $object_json['Codigo_estudiante'])->exists()) {
                $estudiante = Estudiante::where('Codigo', $object_json['Codigo_estudiante'])->first();
                $this->fill_student($estudiante, $object_json);
            } else {
                $estudiante = new Estudiante();
                $estudiante->Codigo = $object_json['Codigo_estudiante'];
                $this->fill_student($estudiante, $object_json);
                NewStudent::dispatch($estudiante);
            }

            // Associate the usuario for the estudiante.
            $usuario = Usuario::where('Codigo_persona', $object_json['Codigo_estudiante'])->first();
            if ($usuario) {
                $estudiante->usuario()->associate($usuario);
                $estudiante->save();
            }

            // Associate the tipo indentificacion for the estudiante.
            $tipo_id = $this->campus->tipos_id()->where('Codigo', $object_json['Codigo_tipo_identificacion'])->first();
            if ($tipo_id) {
                $estudiante->tipo_id()->associate($tipo_id);
                $estudiante->save();
            }

            // Associate the curso for the estudiante.
            $estudiante->cursos()->syncWithoutDetaching($this->curso);
        }
    }
}
