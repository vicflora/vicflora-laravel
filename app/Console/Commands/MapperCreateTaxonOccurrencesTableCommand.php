<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MapperCreateTaxonOccurrencesTableCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vicflora-mapper:create-taxon-occurrences-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create mapper.taxon_occurrences table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Drop existing mapper.taxon_occurrences table');
        DB::statement('DROP TABLE IF EXISTS mapper.taxon_occurrences');

        $this->info('Create new mapper.taxon_occurrences table');
        Schema::create('mapper.taxon_occurrences', function(Blueprint $table) {
            $table->id();
            $table->timestampsTz();
            $table->uuid('taxon_concept_id');
            $table->uuid('occurrence_id');
            $table->string('catalog_number', 32);
            $table->double('latitude');
            $table->double('longitude');
            $table->point('geom', 'GEOMETRY', 4326);
            $table->jsonb('geojson');
            $table->string('data_source', 32);
            $table->uuid('taxon_id');
            $table->uuid('accepted_name_usage_id');
            $table->uuid('species_id');
            $table->string('scientific_name', 128);
            $table->string('accepted_name', 128);
            $table->string('species_name', 128);
            $table->string('occurrence_status', 32)->nullable();
            $table->string('occurrence_status_source', 32)->nullable();
            $table->string('establishment_means', 32)->nullable();
            $table->string('establishment_means_source', 32)->nullable();
            $table->string('degree_of_establishment', 32)->nullable();
            $table->string('degree_of_establishment_source', 32)->nullable();
        });
    }
}
