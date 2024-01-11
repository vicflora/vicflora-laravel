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
    protected $signature = 'vicflora:create-api-documentation {--path=../../public/apidocs/ : Path to copy the documentation to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create API documentation from GraphQL schema';

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
        exec('npx spectaql config.yml');

        $src = 'public/*';
        $dest = $this->option('path');
        $this->info("Copy documentation to '$dest' directory");
        // copy('public/index.html', '../../public/apidocs/index.html');
        `cp -r $src $dest`;

        $this->info('API documentation has been successfully updated');

        return 0;
    }
}
