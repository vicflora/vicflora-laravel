<?php

use App\Database\Migrations\MigrationTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTaxonTreeItemsTable extends Migration
{
    use MigrationTrait;

    protected $tableName = 'taxon_tree_items';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestampTz('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestampTz('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->bigInteger('created_by_id');
            $table->bigInteger('modified_by_id')->nullable();
            $table->smallInteger('version')->default(0);
            $table->bigInteger('taxon_concept_id');
            $table->bigInteger('parent_id')->nullable();
            $table->integer('node_number');
            $table->integer('highest_descendant_node_number');
            $table->smallInteger('depth')->nullable();
            $table->string('path', 512);
            $table->string('name_path', 1024);
            $table->index('taxon_concept_id');
            $table->index('node_number');
            $table->index('highest_descendant_node_number');
            $table->index('path');
            $table->index('name_path');
            $table->foreign('taxon_concept_id')->on('taxon_concepts')->references('id');
            $table->foreign('created_by_id')->on('agents')->references('id');
            $table->foreign('modified_by_id')->on('agents')->references('id');
        });

        //$this->setGlobalSequence();
        $this->setTriggers();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->tableName);
    }
}
