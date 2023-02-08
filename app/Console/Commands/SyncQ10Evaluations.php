<?php

namespace App\Console\Commands;

use App\Models\Term;
use App\Models\Campus;
use App\Services\Q10API;
use App\Models\Evaluation;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SyncQ10Evaluations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:Q10evaluations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronizes the evaluations Q10 database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get the combination of programs and terms from a campus. If the term is not active, it is not included.
     *
     * @param \App\Models\Campus $campus
     *
     * @return \Illuminate\Support\Collection
     */
    public function getProgramsTerms($campus){
        $programs_terms = DB::table('programs')->crossJoin('terms')
            ->where('programs.campus_id', $campus->id)
            ->where('terms.campus_id', $campus->id)
            ->where('Habilitado', true)
            ->get();
        return $programs_terms;
    }

    /**
     * Get the subject instance from the evaluation object.
     *
     * @param \App\Models\Campus $campus
     * @param array $object_json
     *
     * @return \App\Models\Subject
     */
    public function getSubject($campus, $object_json){
        $subject = $campus->subjects()->where('Nombre', $object_json['Nombre_asignatura'])->first();
        return $subject;
    }

    /**
     * Get the course instance from the term.
     *
     * @param \App\Models\Term $term
     * @param array $object_json
     *
     * @return \App\Models\Course
     */
    public function getCourse($term, $object_json){
        $course = $term->courses()->where('Codigo', $object_json['Codigo_curso'])
            ->where('Nombre', $object_json['Nombre_curso'])->first();
        return $course;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DB::disableQueryLog();
        //TODO: Revisar Logica
        foreach (Campus::all() as $campus) {
            Log::debug("Obteniendo todas las evaluciones de un periodo activo", ["Nombre"=>$campus->Nombre]);
            $this->info('Obteniendo todas las evaluaciones de los periodos activos de '.$campus->Nombre);

            // Creo un cliente http para consultar el api de Q10
            $client = new Q10API('/evaluaciones', $campus->Secreto);

            $programs_terms = $this->getProgramsTerms($campus);
            $bar = $this->output->createProgressBar(count($programs_terms));
            $bar->start();
            foreach ($programs_terms as $row) {

                $response = $client->get_paginated([
                    'Programa' => $row->Codigo,
                    'Periodo' => $row->Consecutivo
                ]);

                Log::debug("Numero de evaluaciones a procesar ", [
                    'Programa' => $row->Codigo,
                    'Periodo' => $row->Consecutivo,
                    'Respuesta' => $response->count()
                ]);

                $evaluations = $response->map(function ($object_json) use ($row, $campus) {
                    $term = Term::find($row->id);
                    $subject = $this->getSubject($campus, $object_json);
                    $course = $this->getCourse($term, $object_json);

                    if(is_null($subject) && is_null($course)) {
                        $this->warn("Asignatura ni curso encontrados mientras se procesan las evaluaciones");
                        Log::warning("Asignatura ni curso encontrados mientras se procesan las evaluaciones",
                            [
                                "Sede"=>$campus->Nombre,
                                "Nombre_asignatura"=>$object_json['Nombre_asignatura'],
                                "Nombre_periodo"=>$object_json['Nombre_periodo'],
                                "Nombre_programa"=>$object_json['Nombre_programa'],
                                "Nombre_curso"=>$object_json['Nombre_curso']
                            ]);
                        return;
                    }
                    $evaluation_id = $this->call('q10:checkEvaluation', [
                        'evaluation_json'=>$object_json,
                        'subject'=>$subject,
                        'course'=>$course
                    ]);

                    if ($evaluation_id == 0) {
                        $this->warn("No se pudo crear la evaluacion");
                        Log::warning("No se pudo crear la evaluacion",
                            [
                                "Sede"=>$campus->Nombre,
                                "Nombre_asignatura"=>$object_json['Nombre_asignatura'],
                                "Nombre_periodo"=>$object_json['Nombre_periodo'],
                                "Nombre_programa"=>$object_json['Nombre_programa'],
                                "Nombre_curso"=>$object_json['Nombre_curso']
                            ]);
                        return;
                    }
                    sleep(0.5);
                    return Evaluation::find($evaluation_id);
                });

                sleep(2);
                $bar->advance();
            }
            $bar->finish();
            $this->info(" ¡Evaluaciones sincronizadas de ".$campus->Nombre."!");
        }
        return 0;
    }
}
