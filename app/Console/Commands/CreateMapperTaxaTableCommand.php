<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateMapperTaxaTableCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vicflora-mapper:create-taxa-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes the existing mapper.taxa table and creates 
            a new one';

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
        $this->info('Drop existing mapper.taxa table');
        DB::statement('DROP TABLE IF EXISTS mapper.taxa CASCADE');

        $this->info('Create new mapper.taxa table');
        Schema::create('mapper.taxa', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestampsTz();
            $table->uuid('scientific_name_id');
            $table->string('scientific_name', 128);
            $table->string('scientific_name_authorship', 128)->nullable();
            $table->string('taxon_rank', 32);
            $table->string('taxonomic_status', 32);
            $table->uuid('species_id');
            $table->string('species_name', 128);
            $table->string('species_name_authorship', 128)->nullable();
            $table->uuid('accepted_name_usage_id');
            $table->string('accepted_name', 128);
            $table->string('accepted_name_authorship', 128)->nullable();
            $table->string('accepted_name_rank');
            $table->string('occurrence_status', 32)->nullable();
            $table->string('establishment_means', 32)->nullable();
            $table->string('degree_of_establishment')->nullable();

            $table->index('id');
            $table->index('scientific_name_id');
            $table->index('species_id');
            $table->index('accepted_name_usage_id');
            $table->index('taxon_rank');
        });

    }
}
