<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inaturalist.photos', function (Blueprint $table) {
            $table->id();
            $table->string('license_code');
            $table->json('original_dimensions');
            $table->text('attribution')->nullable();
            $table->string('url');
            $table->integer('license_id')->nullable();
            $table->foreign('license_id')->references('id')->on('licenses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inaturalist.photos');
    }
};
