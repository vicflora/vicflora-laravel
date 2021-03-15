<?php

namespace App\Console\Commands;

use App\Models\TaxonTreeDefItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DataMigrateTaxonTreeDefItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:migrate:taxon-tree-def-items';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load ranks';

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

        $items = $conn->table('vicflora_taxontreedefitem as i')
                ->leftJoin('vicflora_taxontreedefitem as p', 'i.ParentItemID', '=', 'p.TaxonTreeDefItemID')
                ->select('i.name', 'i.TextBefore as text_before', 
                        'i.TextAfter as text_after',
                        'i.FullNameSeparator as full_name_separator', 
                        'i.IsEnforced as is_enforced',
                        'i.IsInFullName as is_in_full_name', 
                        'i.RankID as rank_id', 
                        'p.RankID as parent_rank_id')
                ->get();

        foreach ($items as $item) {
            TaxonTreeDefItem::create([
                'guid' => Str::uuid(),
                'name' => $item->name,
                'text_before' => $item->text_before,
                'text_after' => $item->text_after,
                'full_name_separator' => $item->full_name_separator,
                'is_enforced' => $item->is_enforced,
                'is_in_full_name' => $item->is_in_full_name,
                'rank_id' => $item->rank_id,
                'parent_item_id' => TaxonTreeDefItem::where('rank_id', $item->parent_rank_id)->value('id'),
                'created_by_id' => 1
            ]);
        }
    }
}
