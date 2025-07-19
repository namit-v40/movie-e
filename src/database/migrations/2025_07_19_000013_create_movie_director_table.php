<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovieDirectorTable extends Migration
{
    public function up()
    {
        Schema::create('movie_director', function (Blueprint $table) {
            $table->foreignId('movie_id')->constrained()->onDelete('cascade');
            $table->foreignId('director_id')->constrained()->onDelete('cascade');
            $table->primary(['movie_id', 'director_id']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('movie_director');
    }
}