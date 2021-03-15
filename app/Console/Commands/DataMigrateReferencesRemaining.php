<?php

namespace App\Console\Commands;

use App\Models\Agent;
use App\Models\Reference;
use App\Models\ReferenceType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DataMigrateReferencesRemaining extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:migrate:references:remaining';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load remaining references';

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

        $refs = $conn->table('vicflora_reference as r')
                ->leftJoin('users as cb', 'r.CreatedByID', '=', 'cb.UsersID')
                ->leftJoin('users as mb', 'r.CreatedByID', '=', 'mb.UsersID')
                ->whereNotIn('r.ReferenceType', ['Protologue', 'Article', 'Book', 'Chapter', 'MISSING'])
                ->select('r.guid', 'r.ReferenceType as reference_type', 'r.title', 
                        'r.edition', 'r.PublicationYear as publication_year', 
                        'r.publisher', 'r.PlaceOfPublication as place_of_publication',
                        'cb.Email as created_by', 'mb.Email as modified_by', 
                        'r.TimestampCreated as created_at',
                        'r.TimestampModified as updated_at')
                ->get();
        
        foreach ($refs as $ref) {
            Reference::create([
                'guid' => $ref->guid,
                'reference_type_id' => ReferenceType::where('name', $ref->reference_type)->value('id'),
                'title' => $ref->title,
                'edition' => $ref->edition,
                'publication_year' => $ref->publication_year,
                'publisher' => $ref->publisher,
                'place_of_publication' => $ref->place_of_publication,
                'created_by_id' => $ref->created_by ? Agent::where('email', $ref->created_by)->value('id') : 1,
                'modified_by_id' => $ref->modified_by ? Agent::where('email', $ref->modified_by)->value('id') : null,
                'created_at' => $ref->created_at ? $ref->created_at . '+10' : now(),
                'updated_at' => $ref->updated_at ? $ref->updated_at . '+10' : now(),
            ]);
        }
    }
}
