<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocentesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('docentes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->unsignedBigInteger('tipo_id_id')->nullable();
            $table->string('Codigo');
            $table->string('Primer_nombre')->nullable();
            $table->string('Segundo_nombre')->nullable();
            $table->string('Primer_apellido')->nullable();
            $table->string('Segundo_apellido')->nullable();
            $table->string('Numero_identificacion')->nullable();
            $table->string('Genero')->nullable();
            $table->string('Email')->nullable();
            $table->string('Telefono')->nullable();
            $table->string('Celular')->nullable();
            $table->date('Fecha_nacimiento')->nullable();
            $table->string('Lugar_nacimiento')->nullable();
            $table->string('Lugar_residencia')->nullable();
            $table->string('Direccion')->nullable();
            $table->timestamps();

            $table->foreign('usuario_id')->references('id')->on('usuarios');
            $table->foreign('tipo_id_id')->references('id')->on('tipos_identificacion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('docentes');
    }
}
