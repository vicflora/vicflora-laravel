<?php

namespace App\Console\Commands;

use App\Models\Agent;
use App\Models\Profile;
use App\Models\Reference;
use App\Models\TaxonConcept;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class DataMigrateProfiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:migrate:profiles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load profiles';

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

        $profiles = $conn->table('vicflora_profile as p')
            ->join('vicflora_taxon as t', 'p.TaxonID', '=', 't.TaxonID')
            ->join('vicflora_taxon as a', 'p.AcceptedID', '=', 'a.TaxonID')
            ->leftJoin('vicflora_reference as r', 'p.SourceID', '=', 'r.ReferenceID')
            ->leftJoin('users as cb', 'p.CreatedByID', '=', 'cb.UsersID')
            ->leftJoin('users as mb', 'p.ModifiedByID', '=', 'mb.UsersID')
            ->select('p.guid', 't.guid as taxon_guid', 'a.guid as accepted_guid',
                    'p.profile', 'r.guid as reference_guid',
                    'p.IsCurrent as is_current', 'p.IsUpdated as is_updated',
                    'cb.Email as created_by', 'mb.Email as modified_by',
                    'p.TimestampCreated as created_at',
                    'p.TimestampModified as updated_at')
            ->get();

        foreach ($profiles as $profile) {
            try {
                Profile::create([
                    'guid' => $profile->guid,
                    'taxon_concept_id' => TaxonConcept::where('guid', $profile->taxon_guid)->value('id'),
                    'accepted_id' => TaxonConcept::where('guid', $profile->accepted_guid)->value('id'),
                    'source_id' => Reference::where('guid', $profile->reference_guid)->value('id'),
                    'profile' => $profile->profile,
                    'is_current' => $profile->is_current,
                    'is_updated' => $profile->is_updated,
                    'created_by_id' => $profile->created_by ? Agent::where('email', $profile->created_by)->value('id') : 1,
                    'modified_by_id' => $profile->modified_by ? Agent::where('email', $profile->modified_by)->value('id') : null,
                    'created_at' => $profile->created_at ? $profile->created_at . '+10' : now(),
                    'updated_at' => $profile->updated_at ? $profile->updated_at . '+10' : now(),
                ]);
            }
            catch (QueryException $e) {
                echo "Taxon Concept {$profile->guid} not found. Profile not loaded.\n";
            }
        }
    }
}
