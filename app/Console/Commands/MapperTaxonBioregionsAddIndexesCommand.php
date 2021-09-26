<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MapperTaxonBioregionsAddIndexesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vicflora-mapper:taxon_bioregions-add-indexes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add indexes to mapper.taxon_bioregions table';

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
        Schema::table('mapper.taxon_bioregions', function(Blueprint $table) {
            $table->index('taxon_concept_id');
            $table->index('scientific_name');
            $table->index('bioregion_id');
            $table->index('bioregion_code');
            $table->index('bioregion_name');
            $table->index('occurrence_status');
            $table->index('establishment_means');
        });
    }
}
