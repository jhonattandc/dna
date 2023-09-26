<?php

namespace App\Q10\Jobs;

use App\Q10\Models\Campus;
use App\Q10\Models\Programa;
use App\Q10\Models\Periodo;
use App\Q10\Models\Evaluacion;
use App\Q10\Models\Estudiante;

use App\Q10\Events\StudentMiss;
use App\Q10\Events\StudentFailed;
use App\Q10\Events\StudentPassed;


use App\Q10\Services\Q10API;
use GuzzleHttp\Exception\ClientException;

use Illuminate\Bus\Queueable;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CampusEvaluacionesSync implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The campus instance.
     *
     * @var \App\Q10\Models\Campus
     */
    protected $campus;

    /**
     * The term instance.
     *
     * @var \App\Q10\Models\Periodo
     */
    protected $periodo;

    /**
     * The program instance.
     *
     * @var \App\Q10\Models\Programa
     */
    protected $programa;
    
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
    public $tries = 1;

    /**
     * The number of seconds the job can run before timing out.
     */
    public $timeout = 300;

    /**
     * Create a new job instance.
     * 
     * @param  \App\Q10\Models\Campus  $campus
     * @param  \App\Q10\Models\Periodo  $periodo
     * @param  \App\Q10\Models\Programa  $programa
     * @param  int  $offset
     * 
     * @return void
     */
    public function __construct(Campus $campus, Periodo $periodo, Programa $programa, $offset = 1)
    {
        $this->campus = $campus;
        $this->periodo = $periodo;
        $this->programa = $programa;
        $this->offset = $offset;
    }

    /**
     * Check if the student miss the subject.
     * 
     * @param \App\Q10\Models\Evaluacion $evaluation
     * @param \App\Q10\Models\Estudiante $student
     *
     * @return void
     */
    public function checkMiss($evaluation, $student){
        // Check for change in evaluation and dispach the asociate event
        // If the students miss the subject
        if ($evaluation->isDirty('Cantidad_inasistencia')){
            if($evaluation->Cantidad_inasistencia > 0){
                event(new StudentMiss($evaluation, $student));
            }
        }
    }

    /**
     * Check if the student pass or fail the subject.
     *
     * @param \App\Q10\Models\Evaluacion $evaluation
     * @param \App\Q10\Models\Estudiante $student
     *
     * @return void
     */
    public function checkPassedOrFailed($evaluation, $student){
        // Check for change in evaluation and dispach the asociate event
        // If the students pass or fail the subject
        if ($evaluation->isDirty('Estado_matricula_asignatura')){
            $estado_matricula = mb_strtolower($evaluation->Estado_matricula_asignatura, 'UTF-8');
            if($estado_matricula == 'no aprobada'){
                event(new StudentFailed($evaluation, $student));
            } else if ($estado_matricula == 'retirada'){
                event(new StudentFailed($evaluation, $student));
            } else if ($estado_matricula == 'cancelado por inasistencia'){
                return;
            } else if($estado_matricula == 'aprobada'){
                event(new StudentPassed($evaluation, $student));
            } else if ($estado_matricula== 'homologacion'){
                event(new StudentPassed($evaluation, $student));
            } else if ($estado_matricula == 'exonerada'){
                event(new StudentPassed($evaluation, $student));
            } else if ($estado_matricula == 'en curso'){
                return;
            } else {
                Log::warning("Estado de matricula no reconocido", ['estado_matricula'=>$estado_matricula]);
            }
        }
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

        $response = $httpClient->get_page('evaluaciones', [
            'headers' => [
                'Api-Key' => $this->campus->Secreto,
            ],
            'query' => [
                'Periodo' => $this->periodo->Consecutivo,
                'Programa' => $this->programa->Codigo,
                'Offset' => $this->offset,
                'Limit' => $this->limit,
            ],
        ]);

        // Check if the request is a client exeption.
        if ($response instanceof ClientException) {
            return;
        }

        if (!$httpClient->check_end($response)) {
            $this->batch()->add(
                new CampusEvaluacionesSync($this->campus, $this->periodo, $this->programa, $this->offset+1)
            );
        }
        $collection = $httpClient->get_collection($response);

        foreach ($collection as $object_json) {
            try {
                $evaluacion = Evaluacion::where('Codigo_estudiante', $object_json['Codigo_estudiante'])
                    ->where('Nombre_periodo', $object_json['Nombre_periodo'])
                    ->where('Codigo_curso', $object_json['Codigo_curso'])
                    ->where('Codigo_matricula', $object_json['Codigo_matricula'])
                    ->firstOrFail();
                $evaluacion->fill($object_json);
                $this->checkMiss($evaluacion, $evaluacion->estudiante);
                $this->checkPassedOrFailed($evaluacion, $evaluacion->estudiante);
                $evaluacion->save();
            } catch (ModelNotFoundException $e) {
                $evaluacion = new Evaluacion($object_json);
                // Asociate the student for the evaluation.
                $estudiante = Estudiante::where('Codigo', $object_json['Codigo_estudiante'])->first();
                if ($estudiante) {
                    $evaluacion->estudiante()->associate($estudiante);
                }
                
                // Asociate the subject for the evaluation.
                $asignatura = $this->campus->asignaturas()->where('Nombre', $object_json['Nombre_asignatura'])->first();
                if ($asignatura) {
                    $evaluacion->asignatura()->associate($asignatura);
                }

                // Asoaciate the course for the evaluation.
                $curso = $this->periodo->cursos()->where('Codigo', $object_json['Codigo_curso'])
                    ->where('Nombre', $object_json['Nombre_curso'])->first();
                if ($curso) {
                    $evaluacion->curso()->associate($curso);
                }
                $evaluacion->save();
            }
        }
    }
}
