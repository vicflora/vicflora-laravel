<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class VbaExportData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vba:export-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
select vba.vba_id, 
	vba.taxon_type as vba_taxon_type, 
	vba.scientific_name as vba_scientific_name, 
	n.full_name as vicflora_taxon_name, 
	ts.label as vicflora_taxonomic_status, 
	an.full_name as vicflora_accepted_name,
	ffg,
	ffg_desc,
	epbc,
	epbc_desc,
	vic_adv,
	vic_adv_desc,
	origin,
	nvis_growth_form
from vba_taxa vba
left join taxon_names n on vba.taxon_name_id=n.id
left join taxon_concepts tc on n.id=tc.taxon_name_id
left join taxonomic_statuses ts on tc.taxonomic_status_id=ts.id
left join taxon_concepts ac on tc.accepted_id=ac.id
left join taxon_names an on ac.taxon_name_id=an.id
where vba.taxon_type in ('Monocotyledons', 'Conifers', 'Ferns and Allies', 'Dicotyledons')
order by vba.taxon_type, vba.scientific_name;
SQL;

        $recs = DB::select($sql);
        $export = fopen(storage_path('app/vba_export.csv'), 'w');

        fputcsv($export, array_keys((array) $recs[0]));
        foreach ($recs as $rec) {
            fputcsv($export, array_values((array) $rec));
        }
        fclose($export);
    }
}
