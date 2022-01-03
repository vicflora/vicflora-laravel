<?php

namespace App\Console\Commands;

use App\Models\Agent;
use App\Models\Reference;
use App\Models\ReferenceType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DataMigrateReferencesProtologues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:migrate:references:protologues';

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
        $conn = DB::connection('mysql');

        $unclassified = $conn->table('vicflora_name as n')
                ->join('vicflora_taxon as t', 'n.NameID', '=', 't.NameID')
                ->leftJoin('vicflora_taxontree as tr', 't.TaxonID', '=', 'tr.TaxonID')
                ->where('t.TaxonomicStatus', 'accepted')
                ->whereNull('tr.TaxonTreeID')
                ->whereNotNull('n.ProtologueID')
                ->select('n.ProtologueID');

        $protologues = $conn->table('vicflora_reference as r')
                ->leftJoin('users as cb', 'r.CreatedByID', '=', 'cb.UsersID')
                ->leftJoin('users as mb', 'r.CreatedByID', '=', 'mb.UsersID')
                ->leftJoinSub($unclassified, 'sub', function($join) {
                    $join->on('r.ReferenceID', '=', 'sub.ProtologueID');
                })
                ->whereNull('sub.ProtologueID')
                ->where('r.ReferenceType', 'Protologue')
                ->select('r.guid', DB::raw('coalesce(r.JournalOrBook, r.Title) as title'),
                        'r.volume', 'r.page', 'r.PublicationYear as publication_year',
                        'cb.Email as created_by', 'mb.Email as modified_by',
                        'r.TimestampCreated as created_at',
                        'r.TimestampModified as updated_at')
                ->get();

        $refType = ReferenceType::where('name', 'Protologue')->first();

        foreach ($protologues as $protologue) {
            Reference::create([
                'reference_type_id' => $refType->id,
                'guid' => $protologue->guid,
                'title' => $protologue->title,
                'volume' => $protologue->volume,
                'pages' => $protologue->page,
                'publication_year' => $protologue->publication_year,
                'created_by_id' => $protologue->created_by ? Agent::where('email', $protologue->created_by)->value('id') : 1,
                'modified_by_id' => $protologue->modified_by ? Agent::where('email', $protologue->modified_by)->value('id') : null,
                'created_at' => $protologue->created_at ? $protologue->created_at . '+10' : now(),
                'updated_at' => $protologue->updated_at ? $protologue->updated_at . '+10' : now(),
            ]);
        }
    }
}
