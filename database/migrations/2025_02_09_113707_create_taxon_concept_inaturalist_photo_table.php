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
        Schema::create('taxon_concept_inaturalist_photo', function (Blueprint $table) {
            $table->integer('taxon_concept_id');
            $table->integer('inaturalist_photo_id');
            $table->index('taxon_concept_id');
            $table->index('inaturalist_photo_id');
            $table->unique(['taxon_concept_id', 'inaturalist_photo_id']);
            $table->foreign('taxon_concept_id')->references('id')->on('taxon_concepts')->onDelete('cascade');
            $table->foreign('inaturalist_photo_id')->references('id')->on('inaturalist.photos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('taxon_concept_inaturalist_photo');
    }
};
