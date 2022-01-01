<?php

namespace App\Console\Commands;

use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MapperGetTaxonParkReservesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vicflora-mapper:get-taxon-park-reserves';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate mapper.taxon_park_reserves table';

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

        $this->info('Recreate mapper.taxon_park_reserves table');
        Log::channel('mapper')->info('Recreate mapper.taxon_park_reserves table');
        $this->call('vicflora-mapper:create-taxon-park-reserves-table');

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

            $parks = DB::table('mapper.taxon_occurrences as o')
                    ->join('mapper.park_reserves as pr', function($join) {
                        $join->whereRaw('ST_Intersects(o.geom, pr.geom)');
                    })
                    ->where('o.taxon_concept_id', $taxon->id)
                    ->select(
                            DB::raw('now() as created_at'),
                            'o.taxon_concept_id',
                            'pr.id as park_reserve_id',
                            'pr.name as park_reserve_name',
                            'pr.name_short as park_reserve_short_name',
                            'pr.area_type as park_reserve_area_type',
                            DB::raw("CASE
                                        WHEN 'present' = ANY (array_agg(o.occurrence_status)::text[]) THEN 'present'
                                        WHEN 'endemic' = ANY (array_agg(o.occurrence_status)::text[]) THEN 'present'
                                        WHEN 'extinct' = ANY (array_agg(o.occurrence_status)::text[]) THEN 'extinct'
                                        WHEN 'doubtful' = ANY (array_agg(o.occurrence_status)::text[]) THEN 'doubtful'
                                        ELSE 'present'
                                    END AS occurrence_status"),
                            DB::raw("CASE
                                            WHEN 'native' = ANY (array_agg(o.establishment_means)::text[]) THEN 'native'
                                            WHEN 'naturalised' = ANY (array_agg(o.establishment_means)::text[]) THEN 'naturalised'
                                            WHEN 'introduced' = ANY (array_agg(o.establishment_means)::text[]) THEN 'introduced'
                                            WHEN 'cultivated' = ANY (array_agg(o.establishment_means)::text[]) THEN 'cultivated'
                                            WHEN 'uncertain' = ANY (array_agg(o.establishment_means)::text[]) THEN 'uncertain'
                                            ELSE 'native'
                                    END AS establishment_means")
                    )
                    ->groupByRaw('o.taxon_concept_id, pr.id')
                    ->get();

            if ($parks->count()) {
                foreach ($parks as $park) {
                    $park->scientific_name = $taxon->scientific_name;
                    DB::table('mapper.taxon_park_reserves')->insert((array) $park);
                }
            }

        }
        $this->info('Add indexes');
        Log::channel('mapper')->info('Add indexes');
        $this->call('vicflora-mapper:taxon-park-reserves-add-indexes');

        $end = new DateTime();
        $this->info('Completed: ' . $end->format('Y-m-d H:i:s'));
        Log::channel('mapper')->info('Completed: ' . $end->format('Y-m-d H:i:s'));

        $duration = $start->diff($end);
        $this->info('Duration: ' . $duration->format('%H:%I:%S'));
        Log::channel('mapper')->info('Duration: ' . $duration->format('%H:%I:%S'));
    }
}
