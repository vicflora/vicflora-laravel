<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOccurrencesTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mapper:create-occurrences-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drops and re-creates mapper.occurrences table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        // $this->info('Drop foreign key on mapper.assetions table');
        // Schema::table('mapper.assertions', function(Blueprint $table) {
        //     $table->dropForeign('assertions_occurrence_id_foreign');
        // });

        $this->info('Drop mapper.occurrences table');
        Schema::dropIfExists('mapper.occurrences');

        $this->info('Create mapper.occurrences table');
        Schema::create('mapper.occurrences', function (Blueprint $table) {
            $table->uuid('uuid');
            $table->timestampsTz();
            $table->string('data_resource_uid', 16)->nullable();
            $table->string('institution_code', 8)->nullable();
            $table->string('collection_code')->nullable();
            $table->string('catalog_number', 32);
            $table->string('scientific_name')->nullable();
            $table->string('unprocessed_scientific_name')->nullable();
            $table->double('latitude');
            $table->double('longitude');
            $table->point('geom', 'GEOMETRY', 4326)->nullable();
            $table->bigInteger('parsed_name_id')->nullable();
            $table->integer('data_source_id')->nullable();
            $table->timestampTz('deleted_at')->nullable();
            $table->primary('uuid');
            $table->index('catalog_number');
            $table->spatialIndex('geom');
        });
    }
}
