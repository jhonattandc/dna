<?php

namespace App\Q10\Jobs;

use App\Q10\Models\Campus;
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

class CampusEstudianteSync implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The campus instance.
     *
     * @var \App\Q10\Models\Campus
     */
    protected $campus;

    /**
     * The estudiante codigo.
     *
     * @var string
     */
    protected $codigo;
    
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
     * @param  string  $codigo
     * @param  int  $offset
     * 
     * @return void
     */
    public function __construct(Campus $campus, $codigo, $offset = 1)
    {
        $this->campus = $campus;
        $this->codigo = $codigo;
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

        $response = $httpClient->get_page('estudiantes/'.$this->codigo, [
            'headers' => [
                'Api-Key' => $this->campus->Secreto,
            ]
        ]);

        if ($response->getStatusCode() != 200) {
            $this->fail();
        }

        $student_json = json_decode($response->getBody(), true);

        if (Estudiante::where('Codigo', $student_json['Codigo_estudiante'])->exists()) {
            $estudiante = Estudiante::where('Codigo', $student_json['Codigo_estudiante'])->first();
            $estudiante->fill($student_json);
            $estudiante->save();
        } else {
            $estudiante = new Estudiante();
            $estudiante->Codigo = $student_json['Codigo_estudiante'];
            $estudiante->fill($student_json);
            $estudiante->save();
            event(new NewStudent($estudiante));
        }

        // Associate the usuario for the estudiante.
        $usuario = Usuario::where('Codigo_persona', $student_json['Codigo_estudiante'])->first();
        if ($usuario) {
            $estudiante->usuario()->associate($usuario);
            $estudiante->save();
        }

        // Associate the tipo indentificacion for the estudiante.
        $tipo_id = $this->campus->tipos_id()->where('Codigo', $student_json['Codigo_tipo_identificacion'])->first();
        if ($tipo_id) {
            $estudiante->tipo_id()->associate($tipo_id);
            $estudiante->save();
        }
    }
}
