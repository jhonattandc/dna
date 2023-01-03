<?php

namespace App\Console\Commands;

use App\Models\Campus;
use App\Models\Student;
use App\Services\Q10API;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Q10Students extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:Q10students';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronizes the students from Q10 API';

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
            Log::debug("Obteniendo todos los estudiantes", ["Nombre"=>$campus->Nombre]);
            $this->info('Obteniendo todos los estudiantes de '.$campus->Nombre);
            $client = new Q10API('/estudiantes', $campus->Secreto);

            $terms_timetable_programs =  DB::table('terms')
                ->where('terms.campus_id', $campus->id)
                ->where('terms.Habilitado', true)
                ->crossJoin('sede_timetables', 'terms.campus_id', '=', 'sede_timetables.campus_id')
                ->crossJoin('programs', 'terms.campus_id', '=', 'programs.campus_id')
                ->crossJoin('courses', 'courses.term_id', '=', 'terms.id')
                ->where('courses.Cantidad_estudiantes_matriculados', '>', '0')
                ->select(
                    'terms.Consecutivo as periodo_consecutivo',
                    'sede_timetables.Consecutivo as sede_jornada_consecutivo',
                    'programs.Codigo as programa_codigo',
                    'courses.consecutivo as curso_consecutivo')
                ->get();

            foreach ($terms_timetable_programs as $row) {
                try {
                    $response = $client->get_paginated([
                        'Periodo' => $row->periodo_consecutivo,
                        'Sede_jornada' => $row->sede_jornada_consecutivo,
                        'Programa' => $row->programa_codigo,
                        'Curso' => $row->curso_consecutivo
                    ]);
                } catch (\Throwable $th) {
                    continue;
                }

                Log::debug("Numero de estudiantes a procesar ", [
                    'Periodo' => $row->periodo_consecutivo,
                    'Sede_jornada' => $row->sede_jornada_consecutivo,
                    'Programa' => $row->programa_codigo,
                    'Curso' => $row->curso_consecutivo,
                    'Respuesta' => $response->count()
                ]);

                $students = $response->map(function ($object_json) {
                    $student = Student::where('Codigo_estudiante', $object_json['Codigo_estudiante'])->first();
                    if (is_null($student)) {
                        $student = new Student($object_json);
                    } else {
                        $student->fill($object_json);
                    }
                    $student->save();
                    return $student;
                });
            }
        }
        return 0;
    }
}
