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

class EvaluationsQ10 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:evaluationsQ10';

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
        foreach (Campus::all() as $campus) {
            Log::info("Obteniendo todas las evaluciones de un periodo activo", ["Nombre"=>$campus->Nombre]);
            $this->info('Obteniendo todas las evaluaciones de los periodos activos de '.$campus->Nombre);
            $client = new Q10API('/evaluaciones', $campus->Secreto);

            $programs_terms = DB::table('programs')->crossJoin('terms')
                ->where('programs.campus_id', $campus->id)
                ->where('terms.campus_id', $campus->id)
                ->where('Habilitado', true)
                ->get();

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
                        Log::warning("Asignatura ni curso encontrados", ["response"=>$object_json]);
                        return;
                    }
                    try{
                        $query = Evaluation::where('Codigo_estudiante', $object_json['Codigo_estudiante']);
                        $query = !is_null($subject) ? $query->where('subject_id', $subject->id) : $query;
                        $query = !is_null($course) ? $query->where('course_id', $course->id) : $query;
                        $evaluation = $query->firstOrFail();
                        $evaluation->fill($object_json);
                    } catch (ModelNotFoundException $e) {
                        $evaluation = new Evaluation($object_json);
                        $evaluation->subject_id = !is_null($subject) ? $subject->id : null;
                        $evaluation->course_id = !is_null($course) ? $course->id : null;
                    }
                    $evaluation->save();
                    return $evaluation;
                });
            }
        }
        return 0;
    }
}
