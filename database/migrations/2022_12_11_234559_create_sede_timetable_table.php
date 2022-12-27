<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSedeTimetableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sede_timetables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('campus_id');
            $table->unsignedBigInteger('timetable_id');
            $table->integer('Consecutivo');
            $table->string('Codigo_sede')->nullable();
            $table->string('Nombre_sede')->nullable();
            $table->string('Codigo_jornada')->nullable();
            $table->string('Nombre_jornada')->nullable();
            $table->boolean('Estado')->default(false)->nullable();
            $table->timestamps();

            $table->foreign('campus_id')->references('id')->on('campuses');
            $table->foreign('timetable_id')->references('id')->on('timetables');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sede_timetables');
    }
}
