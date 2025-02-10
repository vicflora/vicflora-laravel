<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RecreateInaturalistTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inaturalist:recreate-tables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recreates the ables in the inaturalist schema';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Recreate tables');

        Schema::dropIfExists('taxon_concept_inaturalist_photo');
        Schema::dropIfExists('inaturalist.observation_photos');
        Schema::dropIfExists('inaturalist.photos');
        Schema::dropIfExists('inaturalist.observations');
        Schema::dropIfExists('inaturalist.users');
        Schema::dropIfExists('inaturalist.taxa');

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

        Schema::create('inaturalist.users', function (Blueprint $table) {
            $table->id();
            $table->string('login');
            $table->string('name')->nullable();
            $table->string('orcid')->nullable();
        });

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
            $table->foreign('taxon_id')->references('id')->on('inaturalist.taxa');
            $table->foreign('user_id')->references('id')->on('inaturalist.users');
        });

        Schema::create('inaturalist.photos', function (Blueprint $table) {
            $table->id();
            $table->string('license_code');
            $table->json('original_dimensions');
            $table->text('attribution')->nullable();
            $table->string('url');
            $table->integer('license_id')->nullable();
            $table->foreign('license_id')->references('id')->on('licenses');
        });

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

        Schema::create('taxon_concept_inaturalist_photo', function (Blueprint $table) {
            $table->integer('taxon_concept_id');
            $table->integer('inaturalist_photo_id');
            $table->index('taxon_concept_id');
            $table->index('inaturalist_photo_id');
            $table->unique(['taxon_concept_id', 'inaturalist_photo_id']);
            $table->foreign('taxon_concept_id')->references('id')->on('taxon_concepts')->onDelete('cascade');
            $table->foreign('inaturalist_photo_id')->references('id')->on('inaturalist.photos')->onDelete('cascade');
        });


        return 0;
    }
}
