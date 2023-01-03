<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTkcoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tkcourses', function (Blueprint $table) {
            $table->id('course_id');
            $table->integer('id');
            $table->string('name', 2048);
            $table->integer('product_id')->nullable();
            $table->json('chapter_ids')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('instructor_id')->nullable();
            $table->boolean('default')->default(false);
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
        Schema::dropIfExists('tkcourses');
    }
}
