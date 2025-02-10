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
        Schema::create('inaturalist.taxa', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('rank', 32);
            $table->integer('rank_level')->nullable();
            $table->integer('parent_id')->nullable();
            $table->json('ancestor_ids');
            $table->index('parent_id');
            $table->index('name');
        });

        Schema::table('inaturalist.observations', function (Blueprint $table) {
            $table->foreign('taxon_id')->references('id')->on('inaturalist.taxa');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inaturalist.observations', function (Blueprint $table) {
            $table->dropForeign('inaturalist_observations_taxon_id_foreign');
        });

        Schema::dropIfExists('inaturalist.taxa');
    }
};
