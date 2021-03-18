<?php

use App\Database\Migrations\MigrationTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTaxonRelationshipsTable extends Migration
{
    use MigrationTrait;

    protected $tableName = 'taxon_relationships';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taxon_relationships', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestampsTz($precision = 0);
            $table->bigInteger('created_by_id');
            $table->bigInteger('modified_by_id')->nullable();
            $table->bigInteger('subject_taxon_concept_id');
            $table->bigInteger('object_taxon_concept_id');
            $table->bigInteger('taxon_relationship_type_id');
            $table->bigInteger('taxon_relationship_component_id')->nullable();
            $table->bigInteger('taxon_relationship_qualifier_id')->nullable();
            $table->bigInteger('relationship_according_to_id');
            $table->text('remarks')->nullable();
            $table->uuid('guid')->nullable();
            $table->smallInteger('version')->default(0);
            $table->index('subject_taxon_concept_id');
            $table->index('object_taxon_concept_id');
            $table->index('taxon_relationship_type_id');
            $table->index('relationship_according_to_id');
            $table->index('taxon_relationship_component_id');
            $table->foreign('subject_taxon_concept_id')->on('taxon_concepts')->references('id');
            $table->foreign('object_taxon_concept_id')->on('taxon_concepts')->references('id');
            $table->foreign('created_by_id')->on('agents')->references('id');
            $table->foreign('modified_by_id')->on('agents')->references('id');
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
        Schema::dropIfExists('taxon_relationships');
    }
}
