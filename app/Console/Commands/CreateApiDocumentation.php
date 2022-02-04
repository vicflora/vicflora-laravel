<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateApiDocumentation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vicflora:create-api-documentation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create API documentation';

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
        $this->info('Save GraphQL Schema to file');
        $this->call('lighthouse:print-schema', ['--write' => true]);

        $this->info("Change working directory to '" . getcwd() . "/resources/spectaql'");
        chdir(getcwd() . '/resources/spectaql');
        $this->info(getcwd());

        $this->info("Create new documentation");
        exec('npx spectaql -JC config.yml');

        $this->info("Copy documentation to 'public/apidocs' directory");
        copy('public/index.html', '../../public/apidocs/index.html');

        $this->info('API documentation has been successfully updated');

        return 0;
    }
}
