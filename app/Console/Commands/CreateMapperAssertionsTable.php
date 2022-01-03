<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMapperAssertionsTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mapper:create-assertions-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create mapper.assertions table';

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
        $this->info('Drop existing table');
        Schema::connection('mapper')->dropIfExists('assertions');

        $this->info('Re-create mapper.assertions table');
        Schema::connection('mapper')->create('assertions', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestampsTz();
            $table->uuid('occurrence_id');
            $table->string('reason')->nullable();
            $table->text('remarks')->nullable();
            $table->integer('term_id');
            $table->integer('term_value_id');
            $table->integer('agent_id')->nullable();
            $table->integer('assertion_source_id')->nullable();
            $table->foreign('occurrence_id')->references('uuid')->on('occurrences');
            $table->foreign('assertion_source_id')->references('id')->on('assertion_sources');
            $table->foreign('term_id')->references('id')->on('terms');
            $table->foreign('term_value_id')->references('id')->on('term_values');
        });
    }
}
