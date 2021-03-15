<?php

use App\Database\Migrations\MigrationTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTaxonAttributesTable extends Migration
{
    use MigrationTrait;

    protected $tableName = 'taxon_attributes';

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
            $table->smallInteger('version')->default(0);
            //$table->uuid('guid');
            $table->bigInteger('created_by_id');
            $table->bigInteger('modified_by_id')->nullable();
            $table->bigInteger('taxon_concept_id');
            $table->bigInteger('attribute_id');
            $table->bigInteger('attribute_value_id');
            $table->text('remarks')->nullable();
            $table->index('taxon_concept_id');
            $table->index('attribute_id');
            $table->index('attribute_value_id');
            $table->foreign('taxon_concept_id')->on('taxon_concepts')->references('id');
            $table->foreign('attribute_id')->on('attributes')->references('id');
            $table->foreign('attribute_value_id')->on('attribute_values')->references('id');
            $table->foreign('created_by_id')->on('agents')->references('id');
            $table->foreign('modified_by_id')->on('agents')->references('id');
        });

        $this->setGlobalSequence();
        $this->setTriggers();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('taxon_attributes');
    }
}
