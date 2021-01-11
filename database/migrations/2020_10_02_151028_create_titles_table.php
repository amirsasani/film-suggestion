<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTitlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('titles', function (Blueprint $table) {
            $table->id();
            $table->string('imdb_id')->unique();
            $table->string('title')->nullable();
            $table->string('thumb')->nullable();
            $table->string('poster')->nullable();
            $table->float('rate')->nullable();
            $table->string('start_year')->nullable();
            $table->string('end_year')->nullable();
            $table->enum('type', ['movie', 'series'])->nullable();
            $table->timestamp('populated_at')->useCurrent()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('titles');
    }
}
