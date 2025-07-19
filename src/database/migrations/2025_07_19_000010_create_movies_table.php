<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMoviesTable extends Migration
{
    public function up()
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('original_name');
            $table->text('description')->nullable();
            $table->string('poster')->nullable();
            $table->string('thumbnail')->nullable();
            $table->integer('release_year');
            $table->integer('duration')->nullable();
            $table->string('episode_current');
            $table->string('episode_total');
            $table->string('quality');
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->foreignId('type_id')->constrained()->onDelete('cascade');
            $table->foreignId('director_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('movies');
    }
}