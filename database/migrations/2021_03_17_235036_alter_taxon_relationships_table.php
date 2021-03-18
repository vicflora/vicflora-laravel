<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTaxonRelationshipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('taxon_relationships', function(Blueprint $table) {
            $table->foreign('relationship_according_to_id')->references('id')->on('references');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('taxon_relationships', function(Blueprint $table) {
            $table->dropForeign('taxon_relationships_relationship_according_to_id_foreign');
        });
    }
}
