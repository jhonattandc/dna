<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJornadaSedeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jornada_sede', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jornada_id');
            $table->unsignedBigInteger('sede_id');
            $table->integer('Consecutivo');
            $table->boolean('Estado')->default(false);
            $table->timestamps();

            $table->foreign('jornada_id')->references('id')->on('jornadas');
            $table->foreign('sede_id')->references('id')->on('sedes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jornada_sede');
    }
}
