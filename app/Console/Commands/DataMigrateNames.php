<?php

namespace App\Console\Commands;

use App\Models\Agent;
use App\Models\NameType;
use App\Models\Reference;
use App\Models\TaxonName;
use Faker\Guesser\Name;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DataMigrateNames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:migrate:names';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load taxon names';

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

        $names = $conn->table('vicflora_name as n')
                ->leftJoin('vicflora_reference as r', 'n.ProtologueID', '=', 'r.ReferenceID')
                ->leftJoin('users as cb', 'n.CreatedByID', '=', 'cb.UsersID')
                ->leftJoin('users as mb', 'n.ModifiedByID', '=', 'mb.UsersID')
                ->select('n.guid', 'n.Name as name_element', 
                        'n.FullName as full_name', 'n.author', 
                        'n.FullNameWithAuthor as full_name_with_authorship', 
                        'n.NomenclaturalNote as nomenclatural_note',
                        'n.name_type', 'r.GUID as protologue_guid', 
                        'cb.Email as created_by', 'mb.Email as modified_by',
                        'n.TimestampCreated as created_at', 
                        'n.TimestampModified as updated_at')
                ->get();
        
        foreach ($names as $name) {
            if ($name->name_type) {
                $nameType = NameType::where('name', Str::camel($name->name_type))
                        ->first();
                if (!$nameType) {
                    $nameType = NameType::create([
                        'guid' => Str::uuid(),
                        'name' => Str::camel($name->name_type),
                        'label' => Str::title($name->name_type),
                        'created_by_id' => 1
                    ]);
                }

                TaxonName::create([
                    'guid' => $name->guid,
                    'name_part' => $name->name_element,
                    'full_name' => $name->full_name,
                    'authorship' => $name->author,
                    'full_name_with_authorship' => $name->full_name_with_authorship,
                    'nomenclatural_note' => $name->nomenclatural_note,
                    'name_type_id' => isset($nameType) ? $nameType->id : null,
                    'protologue_id' => Reference::where('guid', $name->protologue_guid)->value('id'),
                    'created_by_id' => $name->created_by ? Agent::where('email', $name->created_by)->value('id') : 1,
                    'modified_by_id' => $name->modified_by ? Agent::where('email', $name->modified_by)->value('id') : null,
                    'created_at' => $name->created_at ? $name->created_at . '+10' : now(),
                    'updated_at' => $name->updated_at ? $name->updated_at . '+10' : now(),
                ]);
            }
        }
    }
}
