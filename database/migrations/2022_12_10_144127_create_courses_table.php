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
            $table->unsignedBigInteger('term_id');
            $table->integer('Consecutivo');
            $table->string('Codigo')->nullable();
            $table->string('Nombre')->nullable();
            $table->string('Codigo_docente')->nullable();
            $table->string('Nombre_docente')->nullable();
            $table->integer('Cupo_maximo')->nullable()->default(0);
            $table->integer('Consecutivo_periodo');
            $table->string('Nombre_periodo')->nullable();
            $table->date('Fecha_inicio')->nullable();
            $table->date('Fecha_fin')->nullable();
            $table->integer('Cantidad_estudiantes_matriculados')->nullable()->default(0);
            $table->timestamps();

            $table->foreign('term_id')->references('id')->on('terms');
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
