<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MapperDropDistributionViewsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vicflora-mapper:drop-distribution-views';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop distribution views';

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
        $this->info('Drop mapper.taxon_bioregions_view');
        DB::statement('DROP VIEW IF EXISTS mapper.taxon_bioregions_view');
        $this->info('Drop mapper.taxon_local_government_areas_view');
        DB::statement('DROP VIEW IF EXISTS mapper.taxon_local_government_areas_view');
        $this->info('Drop mapper.taxon_park_reserves_view');
        DB::statement('DROP VIEW IF EXISTS mapper.taxon_park_reserves_view');
    }
}
