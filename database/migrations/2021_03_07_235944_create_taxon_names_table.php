<?php

use App\Database\Migrations\MigrationTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTaxonNamesTable extends Migration
{

    use MigrationTrait;

    protected $tableName = 'taxon_names';
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
            $table->bigInteger('protologue_id')->nullable();
            $table->bigInteger('nomenclatural_status_id')->nullable();
            $table->bigInteger('name_type_id')->nullable();
            $table->bigInteger('basionym_id')->nullable();
            $table->bigInteger('replaced_synonym_id')->nullable();
            $table->bigInteger('created_by_id');
            $table->bigInteger('modified_by_id')->nullable();
            $table->string('name_part', 64);
            $table->string('full_name');
            $table->string('authorship')->nullable();
            $table->string('full_name_with_authorship')->nullable();
            $table->string('nomenclatural_note')->nullable();
            $table->text('remarks')->nullable();
            $table->smallInteger('version')->default(0);
            $table->uuid('guid')->nullable();
            $table->index('guid');
            $table->index('name_part');
            $table->index('full_name');
            $table->index('authorship');
            $table->index('protologue_id');
            $table->index('nomenclatural_status_id');
            $table->index('name_type_id');
            $table->foreign('created_by_id')->on('agents')->references('id');
            $table->foreign('modified_by_id')->on('agents')->references('id');
            $table->foreign('protologue_id')->on('references')->references('id');
            $table->foreign('basionym_id')->on('taxon_names')->references('id');
            $table->foreign('replaced_synonym_id')->on('taxon_names')->references('id');
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
        Schema::dropIfExists('taxon_names');
    }
}
