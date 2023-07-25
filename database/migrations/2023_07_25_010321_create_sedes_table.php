<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSedesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sedes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('campus_id');
            $table->string('Codigo');
            $table->string('Nombre')->nullable();
            $table->string('Telefono')->nullable();
            $table->string('Fax')->nullable();
            $table->string('Direccion')->nullable();
            $table->string('Email')->nullable();
            $table->string('Pagina_web')->nullable();
            $table->boolean('Estado')->default(false);
            $table->timestamps();

            $table->foreign('campus_id')->references('id')->on('campuses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sedes');
    }
}
