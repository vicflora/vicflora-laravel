<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SolariumDeleteService;
use Solarium\Client;

class SolrDeleteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'solr:delete {--uuid=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete documents from the SOLR index. If UUID is 
        provided, only the document with that id is deleted; otherwise all 
        documents will be deleted.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Client $client)
    {
        parent::__construct();
        $this->deleteService = new SolariumDeleteService($client);
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->option('uuid')) {
            $this->deleteService->deleteById($this->option('uuid'));
        }
        else {
            $this->deleteService->deleteByQuery('*:*');
        }
        return 0;
    }
}
