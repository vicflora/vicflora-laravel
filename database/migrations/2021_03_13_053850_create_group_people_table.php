<?php

use App\Database\Migrations\MigrationTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupPeopleTable extends Migration
{
    use MigrationTrait;

    protected $tableName = 'group_people';

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
            $table->smallInteger('version')->default(0);
            //$table->uuid('guid');
            $table->bigInteger('created_by_id');
            $table->bigInteger('modified_by_id')->nullable();
            $table->bigInteger('group_id');
            $table->bigInteger('member_id');
            $table->tinyInteger('sequence');
            $table->index('group_id');
            $table->index('member_id');
            $table->index('sequence');
            $table->foreign('group_id')->references('id')->on('agents');
            $table->foreign('member_id')->references('id')->on('agents');
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
        Schema::dropIfExists($this->tableName);
    }
}
