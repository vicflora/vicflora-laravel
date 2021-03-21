<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOccurrencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mapper')->create('vicflora.occurrences', function (Blueprint $table) {
            $table->uuid('id');
            $table->timestamps();
            $table->string('catalog_number', 32);
            $table->string('data_source', 32);
            $table->float('decimal_latitude');
            $table->float('decimal_longitude');
            $table->string('geo_json');
            $table->uuid('taxon_id');
            $table->uuid('accepted_name_usage_id')->nullable();
            $table->uuid('species_id')->nullable();
            $table->string('scientific_name')->nullable();
            $table->string('accepted_name_usage')->nullable();
            $table->string('species')->nullable();
            $table->string('sub_name_7', 64);
            $table->string('sub_code_7', 16);
            $table->string('reg_name_7', 64);
            $table->string('reg_code_7', 16);
            $table->string('occurrence_status')->nullable();
            $table->string('occurrence_status_source')->nullable();
            $table->string('establishment_means')->nullable();
            $table->string('establishment_means_source')->nullable();
            $table->primary('id');
            $table->index('catalog_number');
            $table->index('data_source');
            $table->index('accepted_name_usage_id');
            $table->index('species_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mapper')->dropIfExists('vicflora.occurrences');
    }
}
