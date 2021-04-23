<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class VbaDropTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vba:drop-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drops vba_taxa table';

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
        Schema::dropIfExists('vba_taxa');
        $this->info('vba_taxa table has been dropped.');
    }
}
