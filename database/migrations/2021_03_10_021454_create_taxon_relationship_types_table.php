<?php

use App\Database\Migrations\MigrationTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTaxonRelationshipTypesTable extends Migration
{
    use MigrationTrait;

    protected $tableName = 'taxon_relationship_types';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taxon_relationship_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestampTz('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestampTz('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('name', 64);
            $table->string('uri', 64)->nullable();
            $table->string('label');
            $table->text('description');
            $table->smallInteger('version')->default(0);
            $table->uuid('guid');
            $table->bigInteger('created_by_id');
            $table->bigInteger('modified_by_id')->nullable();
            $table->index('name');
            $table->index('label');
            $table->index('uri');
            $table->index('guid');
            $table->foreign('created_by_id')->on('agents')->references('id');
            $table->foreign('modified_by_id')->on('agents')->references('id');
        });

        $this->setGlobalSequence();
        $this->setTriggers();

        Schema::table('taxon_relationships', function (Blueprint $table) {
            $table->foreign('taxon_relationship_type_id')->on($this->tableName)->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('taxon_relationships', function(Blueprint $table) {
            $table->dropForeign('taxon_relationships_taxon_relationship_type_id_foreign');
        });

        Schema::dropIfExists('taxon_relationship_types');
    }
}
