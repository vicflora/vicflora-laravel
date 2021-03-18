<?php

use App\Database\Migrations\MigrationTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateNameTypesTable extends Migration
{
    use MigrationTrait;

    protected $tableName = 'name_types';
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
            $table->string('name', 64);
            $table->string('uri', 64)->nullable();
            $table->string('label');
            $table->text('description')->nullable();
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

        Schema::table('taxon_names', function (Blueprint $table) {
            $table->foreign('name_type_id')->on('name_types')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('taxon_names', function (Blueprint $table) {
            $table->dropForeign('taxon_names_name_type_id_foreign');
        });

        Schema::dropIfExists('name_types');
    }
}
