<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('Codigo_estudiante');
            $table->string('Primer_nombre');
            $table->string('Segundo_nombre');
            $table->string('Primer_apellido');
            $table->string('Segundo_apellido');
            $table->string('Codigo_tipo_identificacion');
            $table->string('Numero_identificacion');
            $table->string('Genero');
            $table->string('Email');
            $table->string('Telefono');
            $table->string('Celular');
            $table->date('Fecha_nacimiento');
            $table->string('Lugar_nacimiento');
            $table->string('Lugar_residencia');
            $table->string('Direccion');
            $table->string('Codigo_matricula');
            $table->date('Fecha_matricula');
            $table->string('Condicion_matricula');
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
        Schema::dropIfExists('students');
    }
}
