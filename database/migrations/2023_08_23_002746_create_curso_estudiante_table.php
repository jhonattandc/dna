<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCursoEstudianteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('curso_estudiante', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('curso_id');
            $table->unsignedBigInteger('estudiante_id');
            $table->timestamps();

            $table->foreign('curso_id')->references('id')->on('cursos');
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
        Schema::dropIfExists('curso_estudiante');
    }
}
