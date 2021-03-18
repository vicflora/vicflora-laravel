<?php

use App\Database\Migrations\MigrationTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateVernacularNamesTable extends Migration
{
    use MigrationTrait;

    protected $tableName = 'vernacular_names';
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
            $table->bigInteger('taxon_concept_id');
            $table->string('name', 128);
            $table->boolean('is_preferred')->nullable();
            $table->string('name_usage')->nullable();
            $table->text('remarks')->nullable();
            $table->index('taxon_concept_id');
            $table->index('name');
            $table->index('is_preferred');
            $table->index('guid');
            $table->foreign('taxon_concept_id')->on('taxon_concepts')->references('id');
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
        Schema::dropIfExists('vernacular_names');
    }
}
