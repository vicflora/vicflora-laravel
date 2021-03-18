<?php

use App\Database\Migrations\MigrationTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTaxonConceptsTable extends Migration
{
    use MigrationTrait;

    protected $tableName = 'taxon_concepts';
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
            $table->bigInteger('taxon_name_id');
            $table->bigInteger('according_to_id')->nullable();
            $table->smallInteger('rank_id')->nullable();
            $table->bigInteger('taxon_tree_def_item_id')->nullable();
            $table->bigInteger('accepted_id')->nullable();
            $table->bigInteger('parent_id')->nullable();
            $table->bigInteger('taxonomic_status_id')->nullable();
            $table->bigInteger('occurrence_status_id')->nullable();
            $table->bigInteger('establishment_means_id')->nullable();
            $table->bigInteger('degree_of_establishment_id')->nullable();
            $table->text('remarks')->nullable();
            $table->text('editor_notes')->nullable();
            $table->smallInteger('version')->default(0);
            $table->uuid('guid')->nullable();
            $table->index('taxon_name_id');
            $table->index('according_to_id');
            $table->index('rank_id');
            $table->index('taxon_tree_def_item_id');
            $table->index('accepted_id');
            $table->index('parent_id');
            $table->index('taxonomic_status_id');
            $table->index('occurrence_status_id');
            $table->index('establishment_means_id');
            $table->index('degree_of_establishment_id');
            $table->index('guid');
            $table->foreign('created_by_id')->on('agents')->references('id');
            $table->foreign('modified_by_id')->on('agents')->references('id');
            $table->foreign('taxon_name_id')->references('id')->on('taxon_names');
            $table->foreign('according_to_id')->references('id')->on('references');
            $table->foreign('accepted_id')->references('id')->on('taxon_concepts');
            $table->foreign('parent_id')->references('id')->on('taxon_concepts');
        });
        
        $this->setGlobalSequence();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('taxon_concepts');
    }
}
