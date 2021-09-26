<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MapperTaxonParkReservesAddIndexesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vicflora-mapper:taxon-park-reserves-add-indexes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add indexes to mapper.taxon_park_reserves table';

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
        Schema::table('mapper.taxon_park_reserves', function(Blueprint $table) {
            $table->index('taxon_concept_id');
            $table->index('scientific_name');
            $table->index('park_reserve_id');
            $table->index('park_reserve_name');
            $table->index('park_reserve_short_name');
            $table->index('park_reserve_area_type');
            $table->index('occurrence_status');
            $table->index('establishment_means');
        });
    }
}
