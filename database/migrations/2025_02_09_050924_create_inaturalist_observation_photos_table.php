<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inaturalist.observation_photos', function (Blueprint $table) {
            $table->id();
            $table->integer('position');
            $table->uuid('uuid');
            $table->integer('observation_id');
            $table->integer('photo_id');
            $table->index('observation_id');
            $table->index('photo_id');
            $table->foreign('observation_id')->references('id')->on('inaturalist.observations');
            $table->foreign('photo_id')->references('id')->on('inaturalist.photos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inaturalist.observation_photos');
    }
};
