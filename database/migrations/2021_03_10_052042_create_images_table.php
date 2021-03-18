<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateImagesTable extends Migration
{
    protected $tableName = 'images';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestampTz('timestamp_created')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestampTz('timestamp_modified')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->smallInteger('version')->default(0);
            $table->dateTime('asset_creation_date')->nullable();
            $table->text('caption')->nullable();
            $table->string('catalog_number', 16)->nullable();
            $table->string('copyright_owner', 64)->nullable();
            $table->string('country', 64)->nullable();
            $table->string('country_code', 4)->nullable();
            $table->date('creation_date')->nullable();
            $table->string('creator', 128)->nullable();
            $table->string('cumulus_catalog', 64)->nullable();
            $table->unsignedBigInteger('cumulus_record_id');
            $table->string('cumulus_record_name', 128)->nullable();
            $table->decimal('decimal_latitude', 13, 10)->nullable();
            $table->decimal('decimal_longitude', 13, 10)->nullable();
            $table->boolean('hero_image')->nullable();
            $table->string('license', 36)->nullable();
            $table->string('locality', 256)->nullable();
            $table->dateTime('modified')->nullable();
            $table->string('originating_program', 128)->nullable();
            $table->integer('pixel_x_dimension');
            $table->integer('pixel_y_dimension');
            $table->tinyInteger('rating')->nullable();
            $table->string('recorded_by', 128)->nullable();
            $table->string('record_number', 64)->nullable();
            $table->text('rights')->nullable();
            $table->string('scientific_name', 128)->nullable();
            $table->string('source', 256)->nullable();
            $table->string('state_province', 64)->nullable();
            $table->string('subject_category', 128)->nullable();
            $table->string('subject_orientation', 256)->nullable();
            $table->string('subject_part', 64)->nullable();
            $table->string('subtype', 64)->nullable();
            $table->string('title', 256)->nullable();
            $table->string('type', 64)->nullable();
            $table->string('uid', 64)->nullable();
            $table->bigInteger('taxon_id');
            $table->bigInteger('accepted_id');
            $table->index('cumulus_record_id');
            $table->index('cumulus_catalog');
            $table->index('uid');
            $table->index('taxon_id');
            $table->index('accepted_id');
            $table->foreign('taxon_id')->on('taxon_concepts')->references('id');
            $table->foreign('accepted_id')->on('taxon_concepts')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('images');
    }
}
