<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MapperMatchParsedNamesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vicflora-mapper:match-parsed-names';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Matches parsed names from occurrences to taxon 
            concepts in VicFlora';

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
        $reset = <<<SQL
update mapper.parsed_names
set vicflora_scientific_name_id=null, name_match_type=null, vicflora_taxon_id=null
SQL;

        $canonicalNameMatch = <<<SQL
update mapper.parsed_names
set vicflora_scientific_name_id=taxa.scientific_name_id, 
	name_match_type='canonicalNameMatch',
	vicflora_taxon_id=taxa.accepted_name_usage_id
from mapper.taxa
where canonical_name_with_marker=taxa.scientific_name
SQL;

        $canonicalNameWithAuthorshipMatch = <<<SQL
update mapper.parsed_names
set vicflora_scientific_name_id=taxa.scientific_name_id, 
	name_match_type='canonicalNameWithAuthorshipMatch', 
	vicflora_taxon_id=taxa.accepted_name_usage_id
from mapper.taxa
where canonical_name_complete=concat_ws(' ', taxa.scientific_name, taxa.scientific_name_authorship)
SQL;

        $exactMatch = <<<SQL
update mapper.parsed_names
set vicflora_scientific_name_id=taxa.scientific_name_id, 
	name_match_type='exactMatch', 
	vicflora_taxon_id=taxa.accepted_name_usage_id
from mapper.taxa
where parsed_names.scientific_name=concat_ws(' ', taxa.scientific_name, taxa.scientific_name_authorship)
SQL;

        $affected = DB::update($reset);
        $this->info('reset: ' . $affected . ' rows affected');

        $affected = DB::update($canonicalNameMatch);
        $this->info('canonical name matches: ' . $affected . ' rows affected');

        $affected = DB::update($canonicalNameWithAuthorshipMatch);
        $this->info('canonical name with authorship matches: ' . $affected . ' rows affected');
        
        $affected = DB::update($exactMatch);
        $this->info('exact matches: ' . $affected . ' rows affected');
    }
}
