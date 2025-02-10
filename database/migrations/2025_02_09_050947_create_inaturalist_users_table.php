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
        Schema::create('inaturalist.users', function (Blueprint $table) {
            $table->id();
            $table->string('login');
            $table->string('name')->nullable();
            $table->string('orcid')->nullable();
        });

        Schema::table('inaturalist.observations', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('inaturalist.users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inaturalist.observations', function (Blueprint $table) {
            $table->dropForeign('inaturalist_observations_user_id_foreign');
        });

        Schema::dropIfExists('inaturalist.users');
    }
};
