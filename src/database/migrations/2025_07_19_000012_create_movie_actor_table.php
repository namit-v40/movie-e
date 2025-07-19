<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovieActorTable extends Migration
{
    public function up()
    {
        Schema::create('movie_actor', function (Blueprint $table) {
            $table->foreignId('movie_id')->constrained()->onDelete('cascade');
            $table->foreignId('actor_id')->constrained()->onDelete('cascade');
            $table->primary(['movie_id', 'actor_id']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('movie_actor');
    }
}