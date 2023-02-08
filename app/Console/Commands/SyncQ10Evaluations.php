<?php

namespace App\Console\Commands;

use App\Models\Term;
use App\Models\Campus;
use App\Models\Evaluation;
use App\Models\Subject;
use App\Models\Program;
use App\Models\Course;
use App\Models\Student;

use App\Services\Q10API;

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
     * Get the student instance from evaluation.
     *
     * @param array $object_json
     *
     * @return \App\Models\Student
     */
    public function getStudent($object_json){
        $student = Student::where('Codigo_estudiante', $object_json['Codigo_estudiante'])->first();
        return $student;
    }

    /**
     * Check if the student is enrolled on the subject.
     *
     * @param \App\Models\Course $course
     * @param \App\Models\Subject $subject
     *
     * @return \App\Models\Evaluation
     */
    public function checkStudentSubject(Student $student, Subject $subject){
        try{
            if (is_null($student->tk_id)){
                $this->warn('Estudiante no tiene cuenta en Thinkific: ' . $student->Codigo_estudiante);
                return;
            }

            if (is_null($subject->tkcourse)){
                $this->warn('Asignatura ' . $subject->id . ' no tiene curso en Thinkific: ' . $subject->Nombre);
                return;
            }

            if (! $student->subjects->contains($subject->id)){
                $res = $this->call('thinkific:enrollQ10student', ['student' => $student, 'tkcourse_id' => $subject->tkcourse->id]);
                if ($res != 0){
                    $this->error('No se pudo matricular el estudiante: ' . $student->Codigo_estudiante . ' - ' . $subject->tkcourse->id);
                    return;
                }
                $student->subjects()->attach($subject->id);
                Log::debug('Estudiante matriculado', ['Codigo_estudiante' => $student->Codigo_estudiante, 'Curso Thinkific' => $subject->tkcourse->id]);
                $this->info('Estudiante matriculado: ' . $student->Codigo_estudiante . ' - ' . $subject->tkcourse->id);
                sleep(1);
            }
        } catch (\Exception $e) {
            Log::error('No se pudo matricular el estudiante', ['Codigo_estudiante' => $student->Codigo_estudiante, 'Subject' => $subject->id, 'Error' => $e->getMessage()]);
            $this->error('No se pudo matricular el estudiante: ' . $student->Codigo_estudiante . ' - ' . $subject->id);
        }
    }

    /**
     * Check if the student is enrolled on the course.
     *
     * @param \App\Models\Course $course
     * @param \App\Models\Subject $subject
     *
     * @return \App\Models\Evaluation
     */
    public function checkStudentCourse(Student $student, Course $course){
        try{
            if (is_null($student->tk_id)){
                $this->warn('Estudiante no tiene cuenta en Thinkific: ' . $student->Codigo_estudiante);
                return;
            }

            if (is_null($course->tkcourse)){
                $this->warn('Curso ' . $course->id . ' no tiene curso en Thinkific: '. $course->Nombre);
                return;
            }

            if (! $student->courses->contains($course->id)){
                $res = $this->call('thinkific:enrollQ10student', ['student' => $student, 'tkcourse_id' => $course->tkcourse->id]);
                if ($res != 0){
                    $this->error('No se pudo matricular el estudiante: ' . $student->Codigo_estudiante . ' - ' . $course->id);
                    return;
                }
                $student->courses()->attach($course->id);
                Log::debug('Estudiante matriculado', ['Codigo_estudiante' => $student->Codigo_estudiante, 'Curso Thinkific' => $course->tkcourse->id]);
                $this->info('Estudiante matriculado: ' . $student->Codigo_estudiante . ' - ' . $course->tkcourse->id);
                sleep(1);
            }


        } catch (\Throwable $th) {
            Log::error('No se pudo matricular el estudiante', ['Codigo_estudiante' => $student->Codigo_estudiante, 'Course' => $course->id]);
            $this->error('No se pudo matricular el estudiante: ' . $student->Codigo_estudiante . ' - ' . $course->id);
        }
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
            Log::info("Obteniendo todas las evaluciones de un periodo activo", ["Nombre"=>$campus->Nombre]);
            $this->info('Obteniendo todas las evaluaciones de los periodos activos de '.$campus->Nombre);

            // Creo un cliente http para consultar el api de Q10
            $client = new Q10API('/evaluaciones', $campus->Secreto);

            $programs_terms = $this->getProgramsTerms($campus);
            $bar = $this->output->createProgressBar(count($programs_terms));
            $bar->start();
            $this->info('Procesando '.$bar->getMaxSteps().' periodos');
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
                    
                    $student = $this->getStudent($object_json);
                    if (! is_null($subject)) {
                        $this->checkStudentSubject($student, $subject);
                    } else {
                        $this->checkStudentCourse($student, $course);
                    }
                    sleep(0.5);
                    return Evaluation::find($evaluation_id);
                });

                sleep(2);
                $bar->advance();
            }
            $bar->finish();
            $this->info(" ¡Evaluaciones sincronizadas de ".$campus->Nombre."!");
            Log::info("Evaluaciones sincronizadas de ".$campus->Nombre);
        }
        return 0;
    }
}
