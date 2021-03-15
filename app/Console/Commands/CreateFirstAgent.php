<?php

namespace App\Console\Commands;

use App\Models\Agent;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CreateFirstAgent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:migrate:create-first-agent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the first Agent record';

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
        Schema::table('agents', function (Blueprint $table) {
            $table->bigInteger('created_by_id')->nullable()->change();
        });

        $agent = Agent::create([
            'name' => 'Niels Klazenga',
            'first_name' => 'Niels',
            'last_name' => 'Klazenga',
            'initials' => 'N.',
            'email' => 'niels.klazenga@rbg.vic.gov.au',
            'guid' => Str::uuid(),
        ]);
        $agent->created_by_id = $agent->id;
        $agent->save();

        Schema::table('agents', function (Blueprint $table) {
            $table->bigInteger('created_by_id')->nullable(false)->change();
        });
    }
}
