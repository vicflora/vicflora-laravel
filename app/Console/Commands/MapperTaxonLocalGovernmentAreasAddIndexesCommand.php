<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MapperTaxonLocalGovernmentAreasAddIndexesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vicflora-mapper:taxon-local-government-areas-add-indexes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add indexes to mapper.taxon_local_government_areas table';

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
        Schema::table('mapper.taxon_local_government_areas', function(Blueprint $table) {
            $table->index('taxon_concept_id');
            $table->index('scientific_name');
            $table->index('local_government_area_id');
            $table->index('local_government_area_name');
            $table->index('local_government_area_abbr_name');
            $table->index('occurrence_status');
            $table->index('establishment_means');
        });
    }
}
