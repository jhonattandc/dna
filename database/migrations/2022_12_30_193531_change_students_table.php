<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('Primer_nombre')->nullable()->change();
            $table->string('Segundo_nombre')->nullable()->change();
            $table->string('Primer_apellido')->nullable()->change();
            $table->string('Segundo_apellido')->nullable()->change();
            $table->string('Codigo_tipo_identificacion')->nullable()->change();
            $table->string('Numero_identificacion')->nullable()->change();
            $table->string('Genero')->nullable()->change();
            $table->string('Email')->nullable()->change();
            $table->string('Telefono')->nullable()->change();
            $table->string('Celular')->nullable()->change();
            $table->date('Fecha_nacimiento')->nullable()->change();
            $table->string('Lugar_nacimiento')->nullable()->change();
            $table->string('Lugar_residencia')->nullable()->change();
            $table->string('Direccion')->nullable()->change();
            $table->string('Codigo_matricula')->nullable()->change();
            $table->date('Fecha_matricula')->nullable()->change();
            $table->string('Condicion_matricula')->nullable()->change();
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
