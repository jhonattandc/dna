<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->string('Codigo_estudiante');
            $table->string('Nombre_completo')->nullable();
            $table->string('Nombre_asignatura')->nullable();
            $table->string('Nombre_periodo')->nullable();
            $table->string('Nombre_programa')->nullable();
            $table->string('Codigo_curso');
            $table->string('Nombre_curso')->nullable();
            $table->string('Codigo_matricula');
            $table->float('Promedio_evaluacion')->default(0);
            $table->string('Estado_matricula_asignatura')->nullable();
            $table->float('Porcentaje_evaluado')->nullable()->default(0);
            $table->float('Porcentaje_inasistencia')->nullable()->default(0);
            $table->integer('Cantidad_inasistencia')->nullable()->default(0);
            $table->timestamps();

            $table->foreign('course_id')->references('id')->on('courses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('evaluations');
    }
}
