<?php

use App\Database\Migrations\MigrationTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateProfilesTable extends Migration
{
    use MigrationTrait;

    protected $tableName = 'profiles';

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
            $table->uuid('guid')->nullable();
            $table->bigInteger('taxon_concept_id');
            $table->bigInteger('accepted_id');
            $table->bigInteger('taxonomic_status_id')->nullable();
            $table->longText('profile');
            $table->bigInteger('source_id')->nullable();
            $table->boolean('is_current')->nullable();
            $table->boolean('is_updated')->nullable();
            $table->index('guid');
            $table->index('taxon_concept_id');
            $table->index('accepted_id');
            $table->foreign('taxon_concept_id')->on('taxon_concepts')->references('id');
            $table->foreign('accepted_id')->on('taxon_concepts')->references('id');
            $table->foreign('source_id')->on('references')->references('id');
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
        Schema::dropIfExists('profiles');
    }
}
