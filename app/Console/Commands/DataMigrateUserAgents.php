<?php

namespace App\Console\Commands;

use App\Models\Agent;
use App\Models\AgentType;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class DataMigrateUserAgents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:migrate:user-agents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add agent records for users';

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
        Agent::create([
            'guid' => Str::uuid(),
            'name' => 'Walsh, N.G.',
            'agent_type_id' => AgentType::where('name', 'Person')->value('id'),
            'email' => 'neville.walsh@rbg.vic.gov.au',
            'last_name' => 'Walsh',
            'first_name' => 'Neville',
            'initials' => 'N.G.',
            'created_by_id' => 1
        ]);

        Agent::create([
            'guid' => Str::uuid(),
            'name' => 'Stajsic, V.',
            'agent_type_id' => AgentType::where('name', 'Person')->value('id'),
            'email' => 'val.stajsic@rbg.vic.gov.au',
            'last_name' => 'Stajsic',
            'first_name' => 'Val',
            'initials' => 'V.',
            'created_by_id' => 1
        ]);

        Agent::create([
            'guid' => Str::uuid(),
            'name' => 'Jeanes, J.A.',
            'agent_type_id' => AgentType::where('name', 'Person')->value('id'),
            'email' => 'jeff.jeanes@rbg.vic.gov.au',
            'last_name' => 'Jeanes',
            'first_name' => 'Jeff',
            'initials' => 'J.A.',
            'created_by_id' => 1
        ]);

        Agent::create([
            'guid' => Str::uuid(),
            'name' => 'Udovicic, F.',
            'agent_type_id' => AgentType::where('name', 'Person')->value('id'),
            'email' => 'frank.udovicic@rbg.vic.gov.au',
            'last_name' => 'Udovicic',
            'first_name' => 'Frank',
            'initials' => 'F.',
            'created_by_id' => 1
        ]);

        Agent::create([
            'guid' => Str::uuid(),
            'name' => 'Cantrill, D.J.',
            'agent_type_id' => AgentType::where('name', 'Person')->value('id'),
            'email' => 'david.cantrill@rbg.vic.gov.au',
            'last_name' => 'Cantrill',
            'first_name' => 'David',
            'initials' => 'D.J.',
            'created_by_id' => 1
        ]);

        Agent::create([
            'guid' => Str::uuid(),
            'name' => 'Messina, A.',
            'agent_type_id' => AgentType::where('name', 'Person')->value('id'),
            'email' => 'andre.messina@rbg.vic.gov.au',
            'last_name' => 'Messina',
            'first_name' => 'Andre',
            'initials' => 'A.',
            'created_by_id' => 1
        ]);

        Agent::create([
            'guid' => Str::uuid(),
            'name' => 'Ohlsen, D.J.',
            'agent_type_id' => AgentType::where('name', 'Person')->value('id'),
            'email' => 'Daniel.Ohlsen@rbg.vic.gov.au',
            'last_name' => 'Ohlsen',
            'first_name' => 'Daniel',
            'initials' => 'D.J.',
            'created_by_id' => 1
        ]);

        Agent::create([
            'guid' => Str::uuid(),
            'name' => 'Scarlett, N.H.',
            'agent_type_id' => AgentType::where('name', 'Person')->value('id'),
            'email' => 'neville.scarlett@rbg.vic.gov.au',
            'last_name' => 'Scarlett',
            'first_name' => 'Neville',
            'initials' => 'N.H.',
            'created_by_id' => 1
        ]);

        Agent::create([
            'guid' => Str::uuid(),
            'name' => 'Cross, R.',
            'agent_type_id' => AgentType::where('name', 'Person')->value('id'),
            'email' => 'Rob.Cross@rbg.vic.gov.au',
            'last_name' => 'Cross',
            'first_name' => 'Rob',
            'initials' => 'R.',
            'created_by_id' => 1
        ]);

        Agent::create([
            'guid' => Str::uuid(),
            'name' => 'Robinson, A.',
            'agent_type_id' => AgentType::where('name', 'Person')->value('id'),
            'email' => 'Alastair.Robinson@rbg.vic.gov.au',
            'last_name' => 'Robinson',
            'first_name' => 'Alastair',
            'initials' => 'A.',
            'created_by_id' => 1
        ]);
    }
}
