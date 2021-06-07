<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MapperUpdateTaxaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vicflora-mapper:update-taxa';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes all records in the mapper.taxa table and 
            inserts new ones';

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
        $this->info('Delete existing records');
        DB::unprepared('DELETE FROM mapper.taxa');

        $this->info('Insert new records');
        $sql = <<<SQL
INSERT INTO mapper.taxa (
    id, 
    scientific_name_id, 
    scientific_name, 
    scientific_name_authorship, 
    taxon_rank, 
    taxonomic_status,
    species_id,
    species_name,
    species_name_authorship,
    accepted_name_usage_id,
    accepted_name,
    accepted_name_authorship,
    accepted_name_rank,
    occurrence_status,
    establishment_means,
    degree_of_establishment
)
SELECT 
	tc.guid as id, 
	tn.guid as scientific_name_id,
	tn.full_name as scientific_name,
	tn.authorship as scientific_name_authoship,
	ti.name as taxon_rank,
	ts.name as taxonomic_status,
	
	coalesce(pc.guid, tc.guid) as species_id,
	coalesce(pn.full_name, tn.full_name) as species_name,
	coalesce(pn.authorship, tn.authorship) as species_name_authorship,
	
	coalesce(ac.guid, tc.guid) as accepted_name_usage_id,
	coalesce(an.full_name, tn.full_name) as accepted_name,
	coalesce(an.authorship, tn.authorship) as accepted_name_authorship,
	coalesce(ai.name, ti.name) as accepted_name_rank,
	
	os.name as occurrence_status,
	replace(em.name, '(naturalisedInPart(s)OfState)', '')  as establishment_means,
	dof.name as degree_of_establishment
	
FROM public.taxon_concepts tc
JOIN public.taxonomic_statuses ts on tc.taxonomic_status_id=ts.id
JOIN public.taxon_names tn ON tc.taxon_name_id=tn.id
JOIN public.taxon_tree_def_items ti ON tc.taxon_tree_def_item_id=ti.id

LEFT JOIN public.taxon_concepts pc ON tc.parent_id=pc.id AND ti.rank_id>220
LEFT JOIN public.taxon_names pn ON pc.taxon_name_id=pn.id

LEFT JOIN public.taxon_concepts ac ON tc.accepted_id=ac.id
LEFT JOIN public.taxon_names an ON ac.taxon_name_id=an.id
LEFT JOIN public.taxon_tree_def_items ai ON ac.taxon_tree_def_item_id=ai.id

LEFT JOIN public.occurrence_statuses os ON coalesce(ac.occurrence_status_id, tc.occurrence_status_id)=os.id
LEFT JOIN public.establishment_means em ON coalesce(ac.establishment_means_id, tc.establishment_means_id)=em.id
LEFT JOIN public.degree_of_establishment dof ON coalesce(ac.degree_of_establishment_id, tc.degree_of_establishment_id)=dof.id

WHERE ts.name in ('accepted', 'synonym', 'homotypicSynonym', 'heterotypicSynonym')
	AND ti.rank_id>=220
SQL;
        DB::unprepared($sql);
    }
}
