<?php

namespace App\Console\Commands;

use App\Models\AgentType;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class DataMigrateAgentTypes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:migrate:agent-types';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate Agent Types table';

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
        AgentType::create([
            'name' => 'Person',
            'label' => 'Person',
            'guid' => Str::uuid(),
            'created_by_id' => 1
        ]);

        AgentType::create([
            'name' => 'Group',
            'label' => 'Group',
            'guid' => Str::uuid(),
            'created_by_id' => 1
        ]);

        AgentType::create([
            'name' => 'Organization',
            'label' => 'Organisation',
            'guid' => Str::uuid(),
            'created_by_id' => 1
        ]);

        AgentType::create([
            'name' => 'Other',
            'label' => 'Other',
            'guid' => Str::uuid(),
            'created_by_id' => 1
        ]);


    }
}
