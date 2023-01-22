<?php

namespace App\Console\Commands;

use App\Events\Q10StudentNew;
use App\Models\Campus;
use App\Models\Student;
use App\Services\Q10API;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SyncQ10CourseStudents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:Q10courseStudents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronizes the cpurse that the students is enrolled in Q10 API';

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
                    'programs.Codigo as programa_codigo', 'programs.id as program_id',
                    'courses.consecutivo as curso_consecutivo', 'courses.id as course_id', 'courses.course_tk_id as tkcourse_id')
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

                $this->call('q10:enrollCourseProgram', ['course'=>$row->course_id, 'program'=>$row->program_id]);
                $students = $response->map(function ($object_json) use ($row) {
                    try{
                        $student = Student::where('Codigo_estudiante', $object_json['Codigo_estudiante'])->firstOrFail();
                        $student->fill($object_json);
                        $student->save();
                    } catch (ModelNotFoundException $e) {
                        $student = Student::create($object_json);
                        Q10StudentNew::dispatch($student);
                        $this->call('q10:enrollStudentCourse', ['student'=>$student, "course"=>$row->course_id]);
                        if (! is_null($row->tkcourse_id)) {
                            $this->call('thinkific:enrollQ10student', ['student'=>$student, 'tk_course'=>$row->tkcourse_id]);
                        }
                    }
                    return $student;
                });
            }
        }
        return 0;
    }
}
