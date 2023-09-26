<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluaciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('curso_id')->nullable();
            $table->unsignedBigInteger('asignatura_id')->nullable();
            $table->unsignedBigInteger('estudiante_id')->nullable();
            $table->string('Codigo_estudiante')->nullable();
            $table->string('Nombre_periodo')->nullable();
            $table->string('Codigo_curso')->nullable();
            $table->string('Nombre_curso')->nullable();
            $table->float('Promedio_evaluacion')->default(0);
            $table->float('Porcentaje_evaluado')->nullable()->default(0);
            $table->float('Porcentaje_inasistencia')->nullable()->default(0);
            $table->integer('Cantidad_inasistencia')->nullable()->default(0);
            $table->string('Codigo_matricula')->nullable();
            $table->string('Estado_matricula_asignatura')->nullable();
            $table->timestamps();

            $table->foreign('curso_id')->references('id')->on('cursos');
            $table->foreign('asignatura_id')->references('id')->on('asignaturas');
            $table->foreign('estudiante_id')->references('id')->on('estudiantes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('evaluaciones');
    }
}
