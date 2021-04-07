<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MapperLoadOccurrences extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mapper:load-occurrences';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load occurrence data from view into table';

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
        // Drop occurrences table
        Schema::connection('mapper')->dropIfExists('vicflora.occurrences');

        // Create occurrences table
        Schema::connection('mapper')->create('vicflora.occurrences', function (Blueprint $table) {
            $table->uuid('id');
            $table->timestamps();
            $table->string('catalog_number', 32);
            $table->string('data_source', 32);
            $table->float('decimal_latitude');
            $table->float('decimal_longitude');
            $table->point('geom', 4326);
            $table->string('geojson');
            $table->uuid('taxon_id');
            $table->uuid('accepted_name_usage_id')->nullable();
            $table->uuid('species_id')->nullable();
            $table->string('scientific_name')->nullable();
            $table->string('accepted_name_usage')->nullable();
            $table->string('species')->nullable();
            $table->string('sub_name_7', 64);
            $table->string('sub_code_7', 16);
            $table->string('reg_name_7', 64);
            $table->string('reg_code_7', 16);
            $table->string('occurrence_status')->nullable();
            $table->string('occurrence_status_source')->nullable();
            $table->string('establishment_means')->nullable();
            $table->string('establishment_means_source')->nullable();
            $table->primary('id');
            $table->index('catalog_number');
            $table->index('data_source');
            $table->index('accepted_name_usage_id');
            $table->index('species_id');
            $table->spatialIndex('geom');
        });




        $sql = <<<SQL
insert into vicflora.occurrences (id, catalog_number, data_source, 
    decimal_latitude, decimal_longitude, geojson, geom, taxon_id, 
    accepted_name_usage_id, species_id, scientific_name, accepted_name_usage, 
    species, sub_name_7, sub_code_7, reg_name_7, reg_code_7, occurrence_status, 
    occurrence_status_source, establishment_means, establishment_means_source)
select 
    uuid::uuid, 
    catalog_number, 
    data_source, 
    decimal_latitude, 
    decimal_longitude, 
    ST_AsGeoJSON(geom), 
    geom,
    taxon_id::uuid, 
    accepted_name_usage_id::uuid, 
    species_id::uuid, 
    scientific_name, 
    accepted_name_usage, 
    species, 
    sub_name_7, 
    sub_code_7, 
    reg_name_7, 
    reg_code_7, 
    occurrence_status, 
    occurrence_status_source, 
    establishment_means, 
    establishment_means_source
from vicflora.occurrence_view
where uuid<>'0'
SQL;

        // Load occurrences
        DB::connection('mapper')->unprepared(DB::raw($sql));
    }
}
