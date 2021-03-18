<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MigrateAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:migrate:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrates all data';

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
        $this->info('Roll back migrations...');
        Artisan::call('migrate:rollback');
        $this->info('Running migrations...');
        Artisan::call('migrate');
        $this->info('Create administrator agent...');
        Artisan::call('data:migrate:create-first-agent');
        $this->info('Create agent types...');
        Artisan::call('data:migrate:agent-types');
        $this->info('Create agent records for VicFlora editors...');
        Artisan::call('data:migrate:user-agents');
        $this->info('Load references...');
        Artisan::call('data:migrate:references');
        $this->info('Load authors/contributors...');
        Artisan::call('data:migrate:authors');
        $this->info('Load taxon names...');
        Artisan::call('data:migrate:names');
        $this->info('Load ranks...');
        Artisan::call('data:migrate:taxon-tree-def-items');
        $this->info('Load taxa...');
        Artisan::call('data:migrate:taxa');
        $this->info('Load taxon attributes...');
        Artisan::call('data:migrate:taxon-attributes');
        $this->info('Load vernacular names...');
        Artisan::call('data:migrate:vernacular-names');
        $this->info('Load profiles...');
        Artisan::call('data:migrate:profiles');
        $this->info('Load images...');
        Artisan::call('data:migrate:images');
        $this->info('Create taxon tree...');
        Artisan::call('data:create-taxon-tree');

        $this->info('Create home page highlights');
        Artisan::call('data:highlights');
        $this->info('Create carousel slides');
        Artisan::call('data:carousel-slides');

        $this->info('');
        $this->info('Done for now.');
    }
}
