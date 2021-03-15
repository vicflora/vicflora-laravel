<?php

namespace App\Console\Commands;

use App\Models\Agent;
use App\Models\TaxonConcept;
use App\Models\VernacularName;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DataMigrateVernacularNames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:migrate:vernacular-names';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load vernacular names';

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
        $conn = DB::connection('mysql');

        $vernaculars = $conn->table('vicflora_commonname as c')
                ->join('vicflora_taxon as t', 'c.TaxonID', '=', 't.TaxonID')
                ->leftJoin('users as cb', 'c.CreatedByID', '=', 'cb.UsersID')
                ->leftJoin('users as mb', 'c.ModifiedByID', '=', 'mb.UsersID')
                ->select('t.guid as taxon_guid', 'c.CommonName as common_name', 
                        'c.IsPreferred as is_preferred', 'c.NameUsage as name_usage',
                        'cb.Email as created_by', 'mb.Email as modified_by', 
                        'c.TimestampCreated as created_at', 
                        'c.TimestampModified as updated_at')
                ->get();

        foreach ($vernaculars as $name) {
            VernacularName::create([
                'guid' => Str::uuid(),
                'taxon_concept_id' => TaxonConcept::where('guid', $name->taxon_guid)->value('id'),
                'name' => $name->common_name,
                'is_preferred' => $name->is_preferred,
                'name_usage' => $name->name_usage,
                'created_by_id' => $name->created_by ? Agent::where('email', $name->created_by)->value('id') : 1,
                'modified_by_id' => $name->modified_by ? Agent::where('email', $name->modified_by)->value('id') : null,
                'created_at' => $name->created_at ? $name->created_at . '+10' : now(),
                'updated_at' => $name->updated_at ? $name->updated_at . '+10' : now(),
            ]);
        }
    }
}
