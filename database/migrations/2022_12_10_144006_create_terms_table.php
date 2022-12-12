<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTermsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('terms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('campus_id');
            $table->integer('Consecutivo');
            $table->string('Nombre')->nullable();
            $table->date('Fecha_inicio')->nullable();
            $table->date('Fecha_fin')->nullable();
            $table->integer('Ordenamiento')->nullable()->default(1);
            $table->boolean('Estado')->default(false);
            $table->boolean('Habilitado')->default(true);
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
        Schema::dropIfExists('terms');
    }
}
