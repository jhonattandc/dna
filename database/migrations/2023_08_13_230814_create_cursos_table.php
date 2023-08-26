<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCursosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cursos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('periodo_id')->nullable();
            $table->unsignedBigInteger('programa_id')->nullable();
            $table->unsignedBigInteger('jornada_sede_id')->nullable();
            $table->unsignedBigInteger('docente_id')->nullable();
            $table->integer('Consecutivo');
            $table->string('Codigo')->nullable();
            $table->string('Nombre')->nullable();
            $table->integer('Cupo_maximo')->nullable()->default(0);
            $table->date('Fecha_inicio')->nullable();
            $table->date('Fecha_fin')->nullable();
            $table->integer('Cantidad_estudiantes_matriculados')->nullable()->default(0);

            $table->timestamps();

            $table->foreign('periodo_id')->references('id')->on('periodos');
            $table->foreign('programa_id')->references('id')->on('programas');
            $table->foreign('jornada_sede_id')->references('id')->on('jornada_sede');
            $table->foreign('docente_id')->references('id')->on('docentes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cursos');
    }
}
