<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class MapperCreateDistributionViewsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vicflora-mapper:create-distribution-views';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create bioregion, Local Government Area and
            parks and reserves view for use with GeoServer';

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

        $this->info('Drop distribution views');
        $this->call('vicflora-mapper:drop-distribution-views');

        $sql = <<<SQL
CREATE OR REPLACE VIEW mapper.taxon_bioregions_view AS
SELECT tb.id,
    tb.taxon_concept_id,
    tb.scientific_name,
    b.bioregno AS bioregion_number,
    b.bioregion AS bioregion_name,
    b.bioregcode AS bioregion_code,
    tb.occurrence_status,
    tb.establishment_means,
    b.geom
FROM mapper.taxon_bioregions tb
JOIN mapper.bioregions b ON tb.bioregion_id = b.id
SQL;

        $this->info('Create mapper.taxon_bioregions_view');
        DB::statement($sql);


        $sql = <<<SQL
CREATE OR REPLACE VIEW mapper.taxon_local_government_areas_view AS
SELECT tlga.id,
    tlga.taxon_concept_id,
    tlga.scientific_name,
    lga.lga_pid,
    lga.lga_name,
    lga.abb_name,
    tlga.occurrence_status,
    tlga.establishment_means,
    lga.geom
FROM mapper.taxon_local_government_areas tlga
JOIN mapper.local_government_areas lga ON tlga.local_government_area_id = lga.id
SQL;

        $this->info('Create mapper.taxon_local_government_areas_view');
        DB::statement($sql);


        $sql = <<<SQL
CREATE OR REPLACE VIEW mapper.taxon_park_reserves_view AS
SELECT tpr.id,
    tpr.taxon_concept_id,
    tpr.scientific_name,
    pr.id AS park_reserve_id,
    pr.name AS park_reserve_name,
    pr.name_short AS park_reserve_short_name,
    tpr.occurrence_status,
    tpr.establishment_means,
    pr.geom
FROM mapper.taxon_park_reserves tpr
    JOIN mapper.park_reserves pr ON tpr.park_reserve_id = pr.id
SQL;

        $this->info('Create mapper.taxon_park_reserves_view');
        DB::statement($sql);
    }
}
