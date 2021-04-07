<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProtectedAreaOccurrencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mapper')->create('vicflora.protected_area_occurrences', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->uuid('occurrence_id');
            $table->integer('protected_area_id');
            $table->index('occurrence_id');
            $table->index('protected_area_id');
            $table->foreign('occurrence_id')->references('id')->on('vicflora.occurrences');
            $table->foreign('protected_area_id')->references('gid')->on('vicflora.capad_2012');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mapper')->dropIfExists('vicflora.protected_area_occurrences');
    }
}
