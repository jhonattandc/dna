<?php

namespace App\Console\Commands;

use App\Models\Term;
use App\Models\Campus;
use App\Services\Q10API;
use App\Models\Evaluation;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DB::disableQueryLog();
        foreach (Campus::all() as $campus) {
            Log::debug("Obteniendo todas las evaluciones de un periodo activo", ["Nombre"=>$campus->Nombre]);
            $this->info('Obteniendo todas las evaluaciones de los periodos activos de '.$campus->Nombre);
            $client = new Q10API('/evaluaciones', $campus->Secreto);

            $programs_terms = DB::table('programs')->crossJoin('terms')
                ->where('programs.campus_id', $campus->id)
                ->where('terms.campus_id', $campus->id)
                ->where('Habilitado', true)
                ->get();

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
                    $subject = $campus->subjects()->where('Nombre', $object_json['Nombre_asignatura'])->first();
                    $course = $term->courses()
                        ->where('Codigo', $object_json['Codigo_curso'])
                        ->where('Nombre', $object_json['Nombre_curso'])
                        ->first();

                    if(is_null($subject) && is_null($course)) {
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
                    try{
                        $query = Evaluation::where('Codigo_estudiante', $object_json['Codigo_estudiante']);
                        $query = !is_null($subject) ? $query->where('subject_id', $subject->id) : $query;
                        $query = !is_null($course) ? $query->where('course_id', $course->id) : $query;
                        $evaluation = $query->firstOrFail();
                        $evaluation->fill($object_json);
                        $this->call('q10:checkEvaluation', ['evaluation'=>$evaluation]);
                    } catch (ModelNotFoundException $e) {
                        $evaluation = new Evaluation($object_json);
                        $evaluation->subject_id = !is_null($subject) ? $subject->id : null;
                        $evaluation->course_id = !is_null($course) ? $course->id : null;
                        Log::debug("Nueva evaluación creada", ['evaluacion'=>$evaluation->id]);
                    }
                    $evaluation->save();
                    return $evaluation;
                });

                $bar->advance();
            }
            $bar->finish();
            $this->info(" ¡Evaluaciones sincronizadas de ".$campus->Nombre."!");
        }
        return 0;
    }
}
