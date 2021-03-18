<?php

use App\Database\Migrations\MigrationTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateReferencesTable extends Migration
{
    use MigrationTrait;

    protected $tableName = 'refs';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('reference_type_id');
            $table->integer('parent_id')->nullable();
            $table->bigInteger('author_id')->nullable();
            $table->integer('created_by_id');
            $table->integer('modified_by_id')->nullable();
            $table->date('created')->nullable();
            $table->string('publication_year', 16)->nullable();
            $table->string('title', 300);
            $table->string('short_title', 255)->nullable();
            $table->string('edition', 32)->nullable();
            $table->string('volume', 32)->nullable();
            $table->string('issue', 32)->nullable();
            $table->integer('page_start')->nullable();
            $table->integer('page_end')->nullable();
            $table->string('pages', 255)->nullable();
            $table->integer('number_of_pages')->nullable();
            $table->string('publisher', 64)->nullable();
            $table->string('place_of_publication', 128)->nullable();
            $table->text('short_description')->nullable();
            $table->text('abstract')->nullable();
            $table->string('isbn', 32)->nullable();
            $table->string('issn', 32)->nullable();
            $table->string('doi', 32)->nullable();
            $table->text('citation')->nullable();
            $table->string('url', 128)->nullable();
            $table->integer('number')->nullable();
            $table->text('citation_html')->nullable();
            $table->smallInteger('version')->default(0);
            $table->uuid('guid')->nullable();
            $table->timestampsTz($precision = 0);
            $table->index('title');
            $table->index('created');
            $table->index('publication_year');
            $table->index('guid');
            $table->foreign('parent_id', 'references_parent_id_foreign')->on('refs')->references('id');
            $table->foreign('reference_type_id', 'references_reference_type_id_foreign')->on('reference_types')->references('id');
            $table->foreign('author_id', 'references_author_id_foreign')->on('agents')->references('id');
            $table->foreign('created_by_id', 'references_created_by_id_foreign')->on('agents')->references('id');
            $table->foreign('modified_by_id', 'references_modified_by_id_foreign')->on('agents')->references('id');
        });

        $this->setGlobalSequence();
        
        Schema::rename('refs', 'references');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('references');
    }
}
