<?php

use App\Database\Migrations\MigrationTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpecimenImagesTable extends Migration
{
    use MigrationTrait;

    protected $tableName = 'specimen_images';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestampsTz();
            $table->integer('cumulus_record_id');
            $table->string('record_name')->nullable();
            $table->string('catalog_number')->nullable();
            $table->string('ala_image_guid');
            $table->string('title')->nullable();
            $table->text('caption')->nullable();
            $table->string('originating_program', 128)->nullable();
            $table->string('subject_category', 128)->nullable();
            $table->smallInteger('pixel_x_dimension');
            $table->smallInteger('pixel_y_dimension');
            $table->string('scientific_name', 128);
            $table->bigInteger('taxon_concept_id');
            $table->bigInteger('accepted_id');
            $table->index('cumulus_record_id');
            $table->index('catalog_number');
            $table->index('ala_image_guid');
            $table->index('originating_program');
            $table->index('subject_category');
            $table->index('pixel_x_dimension');
            $table->index('pixel_y_dimension');
            $table->index('scientific_name');
            $table->index('taxon_concept_id');
            $table->index('accepted_id');
            $table->foreign('taxon_concept_id')->references('id')->on('taxon_concepts');
            $table->foreign('accepted_id')->references('id')->on('taxon_concepts');
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
