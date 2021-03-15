<?php

use App\Database\Migrations\MigrationTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateAgentsTable extends Migration
{
    use MigrationTrait;

    protected $tableName = 'agents';

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
            $table->bigInteger('agent_type_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('name');
            $table->string('first_name', 64)->nullable();
            $table->string('last_name', 64)->nullable();
            $table->string('initials', 32)->nullable();
            $table->string('email', 128)->nullable();
            $table->string('legal_name', 128)->nullable();
            $table->uuid('guid');
            $table->bigInteger('created_by_id');
            $table->bigInteger('modified_by_id')->nullable();
            $table->smallInteger('version')->default(0);
            $table->index('name');
            $table->index(['last_name', 'first_name']);
            $table->index('email');
            $table->index('guid');
            //$table->foreign('agent_type_id')->on('agent_types')->references('id');
            $table->foreign('created_by_id')->on('agents')->references('id');
            $table->foreign('modified_by_id')->on('agents')->references('id');
            $table->foreign('user_id')->on('users')->references('id');
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
        Schema::dropIfExists('agents');
    }
}
