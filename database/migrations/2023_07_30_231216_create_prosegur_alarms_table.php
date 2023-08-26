<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProsegurAlarmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prosegur_alarms', function (Blueprint $table) {
            $table->id();
            $table->string('email_id')->nullable();
            $table->string('system')->nullable();
            $table->string('location')->nullable();
            $table->string('event')->nullable();
            $table->string('operator')->nullable();
            $table->timestamp('triggered_at')->nullable();
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
        Schema::dropIfExists('prosegur_alarms');
    }
}
