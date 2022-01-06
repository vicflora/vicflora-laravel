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
        // $this->info('Roll back migrations...');
        // $this->call('migrate:rollback');
        // $this->info('Running migrations...');
        // $this->call('migrate');
        $this->info('Drop all tables and re-run all migrations');
        $this->call('migrate:fresh');
        $this->info('Create administrator agent...');
        $this->call('data:migrate:create-first-agent');
        $this->info('Create agent types...');
        $this->call('data:migrate:agent-types');
        $this->info('Create agent records for VicFlora editors...');
        $this->call('data:migrate:user-agents');
        $this->info('Load references...');
        $this->call('data:migrate:references');
        $this->info('Load authors/contributors...');
        $this->call('data:migrate:authors');
        $this->info('Load taxon names...');
        $this->call('data:migrate:names');
        $this->info('Load ranks...');
        $this->call('data:migrate:taxon-tree-def-items');
        $this->info('Load taxa...');
        $this->call('data:migrate:taxa');
        $this->info('Load taxon attributes...');
        $this->call('data:migrate:taxon-attributes');
        $this->info('Load vernacular names...');
        $this->call('data:migrate:vernacular-names');
        $this->info('Load profiles...');
        $this->call('data:migrate:profiles');
        $this->info('Load images...');
        $this->call('data:migrate:images');
        $this->info('Load specimen images...');
        $this->call('data:migrate:specimen-images');
        $this->info('Create taxon tree...');
        $this->call('data:create-taxon-tree');

        $this->info('');
        $this->info('Done for now.');
    }
}
