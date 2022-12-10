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
            $table->string('Codigo_estudiante');
            $table->string('Nombre_completo');
            $table->string('Nombre_asignatura');
            $table->string('Nombre_periodo')->nullable();
            $table->string('Nombre_programa')->nullable();
            $table->string('Codigo_curso')->nullable();
            $table->string('Nombre_curso')->nullable();
            $table->string('Codigo_matricula')->nullable();
            $table->float('Promedio_evaluacion');
            $table->string('Estado_matricula_asignatura')->nullable();
            $table->float('Porcentaje_evaluado');
            $table->float('Porcentaje_inasistencia');
            $table->integer('Cantidad_inasistencia');
            $table->timestamps();
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
