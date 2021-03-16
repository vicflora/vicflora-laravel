<?php

namespace App\Console\Commands;

use App\Models\TaxonConcept;
use App\Models\TaxonTreeItem;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaxonTree extends Command
{
    private $nodeNumber;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:create-taxon-tree';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Taxon tree';

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
        $root = TaxonConcept::whereHas('taxonName', function(Builder $query) {
            $query->where('full_name', 'Life');
        })->first();

        $this->nodeNumber = 1;

        Schema::table('taxon_tree_items', function (Blueprint $table) {
            $table->integer('highest_descendant_node_number')->nullable()->change();
        });


        $this->createTreeNode($root, null, '', '', 0);

        Schema::table('taxon_tree_items', function (Blueprint $table) {
            $table->integer('highest_descendant_node_number')->nullable(false)->change();
        });
    }

    private function createTreeNode($taxon, $parent_id, $path, $namePath, $depth) {
        $path = $path .'/' . $taxon->id;
        $namePath = $namePath . '/' . $taxon->taxonName->full_name;
        $node = TaxonTreeItem::create([
            'taxon_concept_id' => $taxon->id,
            'path' => $path,
            'name_path' => $namePath,
            'depth' => $depth,
            'node_number' => $this->nodeNumber,
            'created_by_id' => 1
        ]);
        $children = TaxonConcept::where('parent_id', $taxon->id)
                ->whereHas('taxonomicStatus', function(Builder $query) {
                    $query->where('name', 'accepted');
                })
                ->get();
        if ($children) {
            foreach ($children as $child) {
                $this->nodeNumber++;
                $this->createTreeNode($child, $taxon->id, $path, $namePath, $depth+1);
            }
        }
        $node->highest_descendant_node_number = $this->nodeNumber;
        $node->save();
    }
}
