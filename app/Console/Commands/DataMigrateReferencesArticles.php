<?php

namespace App\Console\Commands;

use App\Models\Agent;
use App\Models\Reference;
use App\Models\ReferenceType;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DataMigrateReferencesArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:migrate:references:articles';

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

        $articles = $conn->table('vicflora_reference as r')
                ->where('r.ReferenceType', 'Article')
                ->leftJoin('users as cb', 'r.CreatedByID', '=', 'cb.UsersID')
                ->leftJoin('users as mb', 'r.CreatedByID', '=', 'mb.UsersID')
                ->select('r.guid', 'r.title', 'r.JournalOrBook as journal', 'r.volume', 'r.part',
                        DB::raw("if(r.Page like '%-%', substring(trim(r.Page), 1, locate('-', trim(r.Page))-1), null) as page_start"),
                        DB::raw("if(r.Page like '%-%', substring(trim(r.Page), locate('-', trim(r.Page))+1), null) as page_end"),
                        DB::raw("if(r.Page not like '%-%', r.Page, null) as pages"),
                        'r.PublicationYear as publication_year',
                        'cb.Email as created_by', 'mb.Email as modified_by', 
                        "r.TimestampCreated as created_at",
                        'r.TimestampModified as updated_at')
                ->get();

        $refType = ReferenceType::where('name', 'Article')->first();
        
        foreach ($articles as $article) {
            $parent = Reference::where('title', $article->journal)
                    ->whereHas('referenceType', function(Builder $query) {
                        $query->where('name', 'Journal');
                    })->first();

            if (!$parent) {
                $parent = Reference::create([
                    'reference_type_id' => ReferenceType::where('name', 'Journal')->value('id'),
                    'guid' => Str::uuid(),
                    'created_by_id' => 1,
                    'title' => $article->journal,
                ]);
            }

            Reference::create([
                'guid' => $article->guid,
                'title' => $article->title,
                'parent_id' => $parent->id,
                'volume' => $article->volume,
                'issue' => $article->part,
                'page_start' => (int) $article->page_start,
                'page_end' => (int) $article->page_end,
                'pages' => $article->pages,
                'publication_year' => $article->publication_year,
                'reference_type_id' => $refType->id,
                'created_by_id' => $article->created_by ? Agent::where('email', $article->created_by)->value('id') : 1,
                'modified_by_id' => $article->modified_by ? Agent::where('email', $article->modified_by)->value('id') : null,
                'created_at' => $article->created_at ? $article->created_at . '+10' : now(),
                'updated_at' => $article->updated_at ? $article->updated_at . '+10' : now(),
            ]);
        }
    }
}
