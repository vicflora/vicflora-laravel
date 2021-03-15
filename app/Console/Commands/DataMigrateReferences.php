<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class DataMigrateReferences extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:migrate:references';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate References';

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
        // Some cleanup
        Artisan::call('data:migrate:references:cleanup');

        // Reference Types
        Artisan::call('data:migrate:references:reference-types');

        // Protologues
        Artisan::call('data:migrate:references:protologues');

        // Articles
        Artisan::call('data:migrate:references:articles');

        // Books
        Artisan::call('data:migrate:references:books');

        // Chapters
        Artisan::call('data:migrate:references:chapters');

        // Remaining references
        Artisan::call('data:migrate:references:remaining');
    }
}
