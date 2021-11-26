<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SolariumUpdateService;
use Solarium\Client;

class UpdateSolrCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'solr:update {--uuid=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update SOLR index. If a UUID is provided only that record is updated, otherwise all records will be updated';

    protected $client;

    protected $updateService;

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

        $data = $this->updateService->update($this->option('uuid'));
        print_r($data);

        return 0;
    }
}
