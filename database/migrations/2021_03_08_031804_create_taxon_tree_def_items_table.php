<?php

use App\Database\Migrations\MigrationTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTaxonTreeDefItemsTable extends Migration
{
    use MigrationTrait;

    protected $tableName = 'taxon_tree_def_items';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestampsTz($precision = 0);
            $table->bigInteger('created_by_id');
            $table->bigInteger('modified_by_id')->nullable();
            $table->smallInteger('version')->default(0);
            $table->uuid('guid')->nullable();
            $table->string('name', 64);
            $table->string('text_before', 16)->nullable();
            $table->string('text_after', 8)->nullable();
            $table->string('full_name_separator', 4)->nullable();
            $table->boolean('is_enforced')->nullable();
            $table->boolean('is_in_full_name')->nullable();
            $table->smallInteger('rank_id')->nullable();
            $table->bigInteger('parent_item_id')->nullable();
            $table->index('guid');
            $table->index('name');
            $table->index('rank_id');
            $table->foreign('created_by_id')->on('agents')->references('id');
            $table->foreign('modified_by_id')->on('agents')->references('id');
            $table->foreign('parent_item_id')->on('taxon_tree_def_items')->references('id');
        });

        $this->setGlobalSequence();
 
        Schema::table('taxon_concepts', function (Blueprint $table) {
            $table->foreign('taxon_tree_def_item_id')->on('taxon_tree_def_items')->references('id');
        });
   }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('taxon_concepts', function (Blueprint $table) {
            $table->dropForeign('taxon_concepts_taxon_tree_def_item_id_foreign');
        });

        Schema::dropIfExists('taxon_tree_def_items');
    }
}
