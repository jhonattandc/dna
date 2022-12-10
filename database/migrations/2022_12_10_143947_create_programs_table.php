<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('Codigo');
            $table->string('Nombre');
            $table->string('Abreviatura');
            $table->string('Numero_resolucion2')->nullable();
            $table->date('Fecha_resolucion')->nullable();
            $table->boolean('Aplica_preinscripcion')->default(false);
            $table->boolean('Aplica_grupo')->default(false);
            $table->string('Tipo_evaluacion');
            $table->boolean('Estado')->default(false);
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
        Schema::dropIfExists('programs');
    }
}
