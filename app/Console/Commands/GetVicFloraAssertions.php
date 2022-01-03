<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class GetVicFloraAssertions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mapper:get-vicflora-assertions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get VicFlora assertions from old postgis database and save as CSV file';

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
        Config::set('database.connections.postgis-artemis', [
            'driver' => 'pgsql',
            "host" => env('ARTEMIS_POSTGIS_HOST'),
            "database" => env('ARTEMIS_POSTGIS_DATABASE'),
            "username" => env('ARTEMIS_POSTGIS_USERNAME'),
            "password" => env('ARTEMIS_POSTGIS_PASSWORD'),
            "schema" => env('ARTEMIS_POSTGIS_SCHEMA'),
        ]);

        $assertions = DB::connection('postgis-artemis')
                ->table('vicflora_assertion as a')
                ->join('avh_occurrence as o', 'a.uuid', '=', 'o.uuid')
                ->select(
                    'a.assertion_id',
                    'a.uuid',
                    'o.catalog_number',
                    'a.term',
                    'a.asserted_value',
                    'a.timestamp_modified',
                    'a.user_id',
                    'a.reason',
                    'a.comment',
                    'a.assertion_remarks'
                )->get();

        $file = fopen(storage_path('app/vicflora_assertions.csv'), 'w');
        fputcsv($file, array_keys((array) $assertions[0]));
        foreach ($assertions as $assertion) {
            fputcsv($file, array_values((array) $assertion));
        }
        fclose($file);
    }

}
