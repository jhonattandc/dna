<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->integer('Consecutivo');
            $table->string('Codigo');
            $table->string('Nombre');
            $table->string('Codigo_docente');
            $table->string('Nombre_docente');
            $table->integer('Cupo_maximo');
            $table->integer('Consecutivo_periodo');
            $table->string('Nombre_periodo');
            $table->date('Fecha_inicio');
            $table->date('Fecha_fin');
            $table->integer('Cantidad_estudiantes_matriculados');
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
        Schema::dropIfExists('courses');
    }
}
