<?php

namespace App\Console\Commands;

use App\Models\Agent;
use App\Models\Reference;
use App\Models\ReferenceType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DataMigrateReferencesChapters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:migrate:references:chapters';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load Chapters';

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
        // Chapters
        $chapters = $conn->table('vicflora_reference as r')
                ->join('vicflora_reference as p', 'r.InPublicationID', '=', 'p.ReferenceID')
                ->leftJoin('users as cb', 'r.CreatedByID', '=', 'cb.UsersID')
                ->leftJoin('users as mb', 'r.CreatedByID', '=', 'mb.UsersID')
                ->where('r.ReferenceType', 'Chapter')
                ->select('r.guid', 'r.title', 'r.volume', 'r.part',
                        DB::raw("if(r.Page like '%-%', substring(trim(r.Page), 1, locate('-', trim(r.Page))-1), null) as page_start"),
                        DB::raw("if(r.Page like '%-%', substring(trim(r.Page), locate('-', trim(r.Page))+1), null) as page_end"),
                        DB::raw("if(r.Page not like '%-%', r.Page, null) as pages"),
                        'r.PublicationYear as publication_year',  'p.guid as is_part_of',
                        'cb.Email as created_by', 'mb.Email as modified_by', 
                        'r.TimestampCreated as created_at',
                        'r.TimestampModified as updated_at')
                ->get();
        
        $refType = ReferenceType::where('name', 'Chapter')->first();

        foreach ($chapters as $chapter) {
            $parent = Reference::where('guid', $chapter->is_part_of)->first();

            Reference::create([
                'guid' => $chapter->guid,
                'title' => $chapter->title ?: '',
                'parent_id' => $parent->id,
                'volume' => $chapter->volume,
                'issue' => $chapter->part,
                'page_start' => $chapter->page_start,
                'page_end' => $chapter->page_end,
                'pages' => $chapter->pages,
                'publication_year' => $chapter->publication_year,
                'reference_type_id' => $refType->id,
                'created_by_id' => $chapter->created_by ? Agent::where('email', $chapter->created_by)->value('id') : 1,
                'modified_by_id' => $chapter->modified_by ? Agent::where('email', $chapter->modified_by)->value('id') : null,
                'created_at' => $chapter->created_at ? $chapter->created_at . '+10' : now(),
                'updated_at' => $chapter->updated_at ? $chapter->updated_at . '+10' : now(),
            ]);
        }
    }
}
