<?php

namespace App\Console\Commands;

use App\Models\Evaluation;
use App\Models\Subject;
use App\Models\Course;
use App\Models\Student;

use App\Events\Q10StudentMiss;
use App\Events\Q10StudentFailed;
use App\Events\Q10StudentPassed;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Q10CheckEvaluation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'q10:checkEvaluation {evaluation_json} {subject} {course}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for change in evaluation and dispach the asociate event';

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
     * Get the subject instance from the argument.
     *
     * @return \App\Models\Subject
     */
    public function getSubject(){
        $subject = $this->argument('subject');
        if (is_int($subject) || is_string($subject)){
            $subject = Subject::find($subject);
        }
        return $subject;
    }

    /**
     * Get the course instance from the argument.
     *
     * @return \App\Models\Course
     */
    public function getCourse(){
        $course = $this->argument('course');
        if (is_int($course) || is_string($course)){
            $course = Course::find($course);
        }
        return $course;
    }

    /**
     * Get the evaluation instance if exist or raise a exception.
     *
     * @param array $object_json
     * @param mixed $subject
     * @param mixed $course
     *
     * @return \App\Models\Evaluation
     */
    public function getEvaluation($object_json, $subject, $course){
        $query = Evaluation::where('Codigo_estudiante', $object_json['Codigo_estudiante']);
        $query = !is_null($subject) ? $query->where('subject_id', $subject->id) : $query;
        $query = !is_null($course) ? $query->where('course_id', $course->id) : $query;
        $evaluation = $query->firstOrFail();
        $evaluation->fill($object_json);
        return $evaluation;
    }


    /**
     * Get the student instance from evaluation.
     *
     * @param array $object_json
     *
     * @return \App\Models\Student
     */
    public function getStudent($object_json){
        $student = Student::where('Codigo_estudiante', $object_json['Codigo_estudiante'])->firstOrFail();
        return $student;
    }

    /**
     * Check if the student miss the subject.
     *
     * @param \App\Models\Evaluation $evaluation
     * @param \App\Models\Student $student
     *
     * @return void
     */
    public function checkMiss($evaluation, $student){
        // Check for change in evaluation and dispach the asociate event
        // If the students miss the subject
        if ($evaluation->isDirty('Cantidad_inasistencia')){
            if($evaluation->Cantidad_inasistencia > 0){
                event(new Q10StudentMiss($evaluation, $student));
            }
        }
    }

    /**
     * Check if the student pass or fail the subject.
     *
     * @param \App\Models\Evaluation $evaluation
     * @param \App\Models\Student $student
     *
     * @return void
     */
    public function checkPassedOrFailed($evaluation, $student){
        // Check for change in evaluation and dispach the asociate event
        // If the students pass or fail the subject
        if ($evaluation->isDirty('Estado_matricula_asignatura')){
            $estado_matricula = mb_strtolower($evaluation->Estado_matricula_asignatura, 'UTF-8');
            if($estado_matricula == 'no aprobada'){
                event(new Q10StudentFailed($evaluation, $student));
            } else if ($estado_matricula == 'retirada'){
                event(new Q10StudentFailed($evaluation, $student));
            } else if ($estado_matricula == 'cancelado por inasistencia'){
                return;
            } else if($estado_matricula == 'aprobada'){
                event(new Q10StudentPassed($evaluation, $student));
            } else if ($estado_matricula== 'homologacion'){
                event(new Q10StudentPassed($evaluation, $student));
            } else if ($estado_matricula == 'exonerada'){
                event(new Q10StudentPassed($evaluation, $student));
            } else if ($estado_matricula == 'en curso'){
                return;
            } else {
                $this->warn("Estado de matricula no reconocido");
                Log::warning("Estado de matricula no reconocido", ['estado_matricula'=>$estado_matricula]);
            }
        }
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $object_json = $this->argument('evaluation_json');
        $subject = $this->getSubject();
        $course = $this->getCourse();

        try{
            $student = $this->getStudent($object_json);
        } catch (ModelNotFoundException $e) {
            $this->error("Estudiante no encontrado");
            Log::error("Estudiante no encontrado mientras se procesaban las evaluaciones", ['estudiante'=>$object_json['Codigo_estudiante']]);
            return 0;
        }

        try{
            $evaluation = $this->getEvaluation($object_json, $subject, $course);
            $this->checkMiss($evaluation, $student);
            $this->checkPassedOrFailed($evaluation, $student);
        } catch (ModelNotFoundException $e) {
            $evaluation = new Evaluation($object_json);
            $evaluation->subject_id = !is_null($subject) ? $subject->id : null;
            $evaluation->course_id = !is_null($course) ? $course->id : null;
            Log::debug("Nueva evaluación creada", ['evaluacion'=>$evaluation->id]);
        }
        $evaluation->save();
        return $evaluation->id;
    }
}
