<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecommendationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recommendations', function (Blueprint $table)
        {
            $table->id();

            $table->unsignedBigInteger('title_id');
            $table->foreign('title_id')->references('id')->on('titles')->cascadeOnUpdate();

            $table->unsignedBigInteger('recommendation_id');
            $table->foreign('recommendation_id')->references('id')->on('titles')->cascadeOnUpdate();

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
        Schema::dropIfExists('recommendations');
    }
}
