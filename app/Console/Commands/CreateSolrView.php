<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Services\SolariumUpdateService;
use Solarium\Client;

class CreateSolrView extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'solr:create-view';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create view of data as it looks like in the SOLR index';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Client $client)
    {
        parent::__construct();
        $this->updateService = new SolariumUpdateService($client);
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Drop solr_index_view');
        DB::statement('DROP VIEW IF EXISTS solr_index_view');

        $this->info('Create solr_index_view');
        $select = $this->updateService->getSqlString();
        $select = str_replace('?', 'true', $select);
        $sql = <<<SQL
CREATE OR REPLACE VIEW solr_index_view AS
$select
SQL;
        DB::statement($sql);
    }
}
