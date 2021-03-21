<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
        $sql = <<<SQL
insert into vicflora.occurrences (id, catalog_number, data_source, 
    decimal_latitude, decimal_longitude, geo_json, taxon_id, 
    accepted_name_usage_id, species_id, scientific_name, accepted_name_usage, 
    species, sub_name_7, sub_code_7, reg_name_7, reg_code_7, occurrence_status, 
    occurrence_status_source, establishment_means, establishment_means_source)
select 
    uuid::uuid, 
    catalog_number, 
    data_source, 
    decimal_latitude, 
    decimal_longitude, geojson, 
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
from vicflora.occurrence_view_dev
where uuid<>'0'
SQL;
        DB::connection('mapper')->unprepared(DB::raw($sql));
    }
}
