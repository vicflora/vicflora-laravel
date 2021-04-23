<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class VbaCreateTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vba:create-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create vba_taxa table';

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
        Schema::create('vba_taxa', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('vba_id');
            $table->string('scientific_name', 80);
            $table->string('common_name', 60)->nullable();
            $table->string('authority', 50)->nullable();
            $table->string('ffg', 4)->nullable();
            $table->string('ffg_desc', 30)->nullable();
            $table->string('epbc', 4)->nullable();
            $table->string('epbc_desc', 25)->nullable();
            $table->string('vic_adv', 4)->nullable();
            $table->string('vic_adv_desc', 25)->nullable();
            $table->string('restriction', 30)->nullable();
            $table->string('origin', 65)->nullable();
            $table->string('taxon_type', 50)->nullable();
            $table->string('vic_life_form', 50)->nullable();
            $table->string('fire_response', 10)->nullable();
            $table->string('nvis_growth_form', 15)->nullable();
            $table->string('treaty', 30)->nullable();
            $table->string('discipline', 25)->nullable();
            $table->string('taxon_level', 20)->nullable();
            $table->integer('fis_species_number')->nullable();
            $table->date('record_modification_date')->nullable();
            $table->date('version_date')->nullable();
            $table->bigInteger('taxon_name_id')->nullable();
            $table->index('vba_id');
            $table->index('scientific_name');
            $table->index('taxon_name_id');
            $table->foreign('taxon_name_id')->references('id')->on('taxon_names');
        });

        $this->info("Table 'vba_taxa' has been created");
    }
}
