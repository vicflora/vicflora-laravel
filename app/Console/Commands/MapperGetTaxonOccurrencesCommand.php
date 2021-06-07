<?php

namespace App\Console\Commands;

use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MapperGetTaxonOccurrencesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vicflora-mapper:get-taxon-occurrences';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populates the mapper.taxon_occurrences table';

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
        $this->info('Recreate mapper.taxon_occurrences table');
        Log::channel('mapper')->info('Recreate mapper.taxon_occurrences table');
        Artisan::call('vicflora-mapper:create-taxon-occurrences-table');

        $taxa = DB::table('mapper.taxa')
                ->where('taxonomic_status', 'accepted')
                ->select('id', 'taxon_rank', 'scientific_name')
                ->get();

        $start = new DateTime();
        $this->info('Started: ' . $start->format('Y-m-d H:i:s'));
        Log::channel('mapper')->info('Started: ' . $start->format('Y-m-d H:i:s'));

        $count = $taxa->count();
        foreach ($taxa as $index => $taxon) {
            $this->info("Processing $index of $count: $taxon->scientific_name");
            Log::channel('mapper')->info("Processing $index of $count: $taxon->scientific_name");

            $query = DB::table('mapper.occurrences as o')
                    ->join('mapper.data_sources as ds', 'o.data_source_id', '=', 'ds.id')
                    ->leftJoin('mapper.assertions as aest1', function($join) {
                        $join->on('o.uuid', '=', 'aest1.occurrence_id')
                                ->where('aest1.assertion_source_id', '=', 1)
                                ->where('aest1.term_id', '=', 2);
                    })
                    ->leftJoin('mapper.term_values as est1', 'aest1.term_value_id', '=', 'est1.id')

                    ->leftJoin('mapper.assertions as aest2', function($join) {
                        $join->on('o.uuid', '=', 'aest2.occurrence_id')
                                ->where('aest2.assertion_source_id', '=', 2)
                                ->where('aest2.term_id', '=', 2);
                    })
                    ->leftJoin('mapper.term_values as est2', 'aest2.term_value_id', '=', 'est2.id')

                    ->leftJoin('mapper.assertions as aocc', function($join) {
                        $join->on('o.uuid', '=', 'aocc.occurrence_id')
                                ->where('aocc.term_id', '=', 1);
                    })
                    ->leftJoin('mapper.term_values as occ', 'aocc.term_value_id', '=', 'occ.id')

                    ->join('mapper.parsed_names as p', 'o.parsed_name_id', '=', 'p.id')
                    ->join('mapper.taxa as t', 'p.vicflora_scientific_name_id', '=', 't.scientific_name_id')

                    ->select(
                        DB::raw('now() as created_at'),
                        'o.uuid as occurrence_id',
                        'o.catalog_number',
                        'o.longitude',
                        'o.latitude',
                        'o.geom',
                        DB::raw('ST_AsGeoJSON(o.geom) as geojson'),
                        'ds.abbreviation as data_source',
                        't.id AS taxon_id',
                        't.accepted_name_usage_id',
                        't.species_id',
                        't.scientific_name',
                        't.accepted_name',
                        't.species_name',
                        DB::raw("coalesce(occ.value, t.occurrence_status, 'present') as occurrence_status"),
                        DB::raw("CASE
                                    WHEN occ.value IS NOT NULL THEN 'assertion'
                                    WHEN t.occurrence_status IS NOT NULL AND t.occurrence_status <> '' THEN 'taxon'
                                    ELSE NULL
                                END AS occurrence_status_source"),
                        DB::raw("coalesce(est1.value, est2.value, t.establishment_means, 'native') as establishment_means"),
                        DB::raw("CASE
                                    WHEN est1.value IS NOT NULL THEN 'assertion'
                                    WHEN est2.value IS NOT NULL THEN 'avh'
                                    WHEN t.establishment_means IS NOT NULL AND t.establishment_means <> '' THEN 'taxon'
                                    ELSE NULL
                                END AS establishment_means_source")
                );

                if ($taxon->taxon_rank == 'species') {
                    $query->where('t.species_id', $taxon->id);
                }
                else {
                    $query->where('t.id', $taxon->id);
                }

                $occurrences = $query->get();

                foreach($occurrences as $occurrence) {
                    $occurrence->taxon_concept_id = $taxon->id;
                    DB::table('mapper.taxon_occurrences')->insert((array) $occurrence);
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
        Artisan::call('vicflora-mapper:taxon-occurrences-add-indexes');
    }
}
