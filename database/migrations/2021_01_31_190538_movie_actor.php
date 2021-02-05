<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MovieActor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movie_actor', function (Blueprint $table){
            $table->uuid('id');
            $table->uuid('movie_id');
            $table->foreign('movie_id')->references('id')->on('movies')->cascadeOnDelete();
            $table->uuid('actor_id');
            $table->foreign('actor_id')->references('id')->on('actors')->cascadeOnDelete();
            $table->string('role');
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
        Schema::drop('movie_actor');
    }
}
