<?php

use App\Database\Migrations\MigrationTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateContributorsTable extends Migration
{
    use MigrationTrait;

    protected $tableName = 'contributors';

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
            $table->bigInteger('reference_id');
            $table->bigInteger('agent_id');
            $table->bigInteger('contributor_role_id')->nullable();
            $table->bigInteger('created_by_id');
            $table->bigInteger('modified_by_id')->nullable();
            $table->smallInteger('sequence');
            $table->uuid('guid')->nullable();
            $table->smallInteger('version');
            $table->index('reference_id');            
            $table->index('agent_id');            
            $table->index('created_by_id');            
            $table->index('modified_by_id');            
            $table->index('sequence');            
            $table->index('guid')->nullable();
            $table->foreign('reference_id')->on('references')->references('id');
            $table->foreign('agent_id')->on('agents')->references('id');
            $table->foreign('contributor_role_id')->on('contributor_roles')->references('id');
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
        Schema::dropIfExists('contributors');
    }
}
