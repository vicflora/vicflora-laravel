<?php

namespace App\Console\Commands;

use App\Models\Agent;
use App\Models\Reference;
use App\Models\ReferenceType;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DataMigrateReferencesBooks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:migrate:references:books';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load books';

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

        // Books
        $books = $conn->table('vicflora_reference as r')
                ->leftJoin('users as cb', 'r.CreatedByID', '=', 'cb.UsersID')
                ->leftJoin('users as mb', 'r.CreatedByID', '=', 'mb.UsersID')
                ->where('r.ReferenceType', 'Book')
                ->select('r.guid', 'r.title', 'r.series', 'r.edition', 'r.volume', 'r.part', 
                        'r.Page as pages', 'r.PublicationYear as publication_year', 
                        'r.publisher', 'r.PlaceOfPublication as place_of_publication',
                        'cb.Email as created_by', 'mb.Email as modified_by', 
                        'r.TimestampCreated as created_at',
                        'r.TimestampModified as updated_at')
                ->get();

        $refType = ReferenceType::where('name', 'Book')->first();

        foreach ($books as $book) {
            if ($book->series) {
                $series = Reference::where('title', $book->series)
                        ->whereHas('referenceType', function (Builder $query) {
                            $query->where('name', 'BookSeries');
                        })->first();

                if (!$series) {
                    $series = Reference::create([
                        'reference_type_id' => ReferenceType::where('name', 'BookSeries')->value('id'),
                        'guid' => Str::uuid(),
                        'created_by_id' => 1,
                        'title' => $book->series,
                    ]);
                }
            }

            Reference::create([
                'guid' => $book->guid,
                'title' => $book->title,
                'parent_id' => $book->series ? $series->id : null,
                'edition' => $book->edition,
                'volume' => $book->volume,
                'issue' => $book->part,
                'pages' => $book->pages,
                'publication_year' => $book->publication_year,
                'publisher' => $book->publisher,
                'place_of_publication' => $book->place_of_publication,
                'reference_type_id' => $refType->id,
                'created_by_id' => $book->created_by ? Agent::where('email', $book->created_by)->value('id') : 1,
                'modified_by_id' => $book->modified_by ? Agent::where('email', $book->modified_by)->value('id') : null,
                'created_at' => $book->created_at ? $book->created_at . '+10' : now(),
                'updated_at' => $book->updated_at ? $book->updated_at . '+10' : now(),
            ]);
        }
    }
}
