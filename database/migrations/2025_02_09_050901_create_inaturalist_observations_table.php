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
        Schema::create('inaturalist.observations', function (Blueprint $table) {
            $table->id('id');
            $table->timestampsTz();
            $table->uuid('uuid');
            $table->string('quality_grade', 16);
            $table->integer('site_id')->nullable();
            $table->string('license_code')->nullable();
            $table->text('description')->nullable();
            $table->date('observed_on')->nullable();
            $table->string('observed_on_string')->nullable();
            $table->json('observed_on_details')->nullable();
            $table->json('place_ids')->nullable();
            $table->integer('taxon_id');
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->json('geojson')->nullable();
            $table->text('place_guess')->nullable();
            $table->integer('user_id');
            $table->index('taxon_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inaturalist.observations');
    }
};
