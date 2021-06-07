<?php

namespace App\Console\Commands;

use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MapperGetTaxonBioregionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vicflora-mapper:get-taxon-bioregions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate mapper.taxon_bioregions table';

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
        
        $this->info('Recreate mapper.taxon_bioregions table');
        Log::channel('mapper')->info('Recreate mapper.taxon_bioregions table');
        Artisan::call('vicflora-mapper:create-taxon-bioregions-table');

        $taxa = DB::table('mapper.taxa')
                ->where('taxonomic_status', 'accepted')
                ->select('id', 'scientific_name')
                ->get();

        $start = new DateTime();
        $this->info('Started: ' . $start->format('Y-m-d H:i:s'));
        Log::channel('mapper')->info('Started: ' . $start->format('Y-m-d H:i:s'));

        $count = $taxa->count();
        foreach ($taxa as $index => $taxon) {
            $this->info("Processing $index of $count: $taxon->scientific_name");
            Log::channel('mapper')->info("Processing $index of $count: $taxon->scientific_name");

            $bioregions = DB::table('mapper.taxon_occurrences as o')
                    ->join('mapper.bioregion_occurrences as bo', DB::raw('o.occurrence_id::text'), '=', 'bo.occurrence_id')
                    ->join('mapper.bioregions as b', 'bo.bioregion_id', '=', 'b.id')
                    ->where('o.taxon_concept_id', $taxon->id)
                    ->select(
                            DB::raw('now() as created_at'),
                            'o.taxon_concept_id',
                            'b.id as bioregion_id',
                            'b.bioregion as bioregion_name',
                            'b.bioregcode as bioregion_code',
                            DB::raw("CASE
                                        WHEN 'present' = ANY (array_agg(o.occurrence_status)::text[]) THEN 'present'
                                        WHEN 'endemic' = ANY (array_agg(o.occurrence_status)::text[]) THEN 'present'
                                        WHEN 'extinct' = ANY (array_agg(o.occurrence_status)::text[]) THEN 'extinct'
                                        WHEN 'doubtful' = ANY (array_agg(o.occurrence_status)::text[]) THEN 'doubtful'
                                        ELSE NULL
                                    END AS occurrence_status"),
                            DB::raw("CASE
                                            WHEN 'native' = ANY (array_agg(o.establishment_means)::text[]) THEN 'native'
                                            WHEN 'naturalised' = ANY (array_agg(o.establishment_means)::text[]) THEN 'naturalised'
                                            WHEN 'introduced' = ANY (array_agg(o.establishment_means)::text[]) THEN 'introduced'
                                            WHEN 'cultivated' = ANY (array_agg(o.establishment_means)::text[]) THEN 'cultivated'
                                            WHEN 'uncertain' = ANY (array_agg(o.establishment_means)::text[]) THEN 'uncertain'
                                            ELSE NULL
                                    END AS establishment_means"),
                            'b.geom'
                    )
                    ->groupByRaw('o.taxon_concept_id, b.id')
                    ->get();

            foreach ($bioregions as $bioregion) {
                $bioregion->scientific_name = $taxon->scientific_name;
                DB::table('mapper.taxon_bioregions')->insert((array) $bioregion);
            }

        }
        $end = new DateTime();
        $this->info('Completed: ' . $end->format('Y-m-d H:i:s'));
        Log::channel('mapper')->info('Completed: ' . $end->format('Y-m-d H:i:s'));

        $duration = $start->diff($end);
        $this->info('Duration: ' . $duration->format('%H:%I:%S'));
        Log::channel('mapper')->info('Duration: ' . $duration->format('%H:%I:%S'));

        $this->info('Add indexes');
        Log::channel('mapper')->info('Add indexes');
        Artisan::call('vicflora-mapper:taxon_bioregions-add-indexes');

    }
}
