<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use stdClass;

class GetKeyMetadata extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vicflora:get-key-metadata';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gets key metadata from the KeyBase API and stores it in the pathway_keys table';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $now = now();
        $this->info($now->toDateTimeString() . ': getting key metadata...');

        $client = new Client(['base_uri' => 'https://data.rbg.vic.gov.au']);
        $res = $client->request('GET', '/keybase-ws/ws/project_keys_get/10');

        $keys = collect(json_decode($res->getBody()) ?: []);
        foreach ($keys as $index => $key) {
            $res2 = $client->request('GET', '/keybase-ws/ws/key_meta_get/' . $key->id);
            $meta = json_decode($res2->getBody()) ?: null;

            $upsert = [
                'id' => $meta->key_id,
                'created_at' => $meta->timestamp_created,
                'updated_at' => $meta->timestamp_modified,
                'title' => $meta->key_title,
                'taxonomic_scope' => $meta->taxonomic_scope,
                'geographic_scope' => $meta->geographic_scope,
                'created_by' => $meta->created_by->full_name,
                'updated_by' => $meta->modified_by->full_name,
                'taxon_concept_id' => null,
                'created_by_id' => null,
                'updated_by_id' => null,
            ];

            DB::table('pathway_keys')->upsert(
                [$upsert],
                ['id'],
                [
                    'created_at',
                    'updated_at',
                    'title',
                    'taxonomic_scope',
                    'geographic_scope',
                    'created_by',
                    'updated_by',
                    'taxon_concept_id',
                    'created_by_id',
                    'updated_by_id'
                ]
            );
        }

        $tcs = DB::table('taxon_concepts as tc')
                ->join('taxon_names as tn', 'tn.id', '=', 'tc.taxon_name_id')
                ->join('taxonomic_statuses as ts', 'tc.taxonomic_status_id', '=', 'ts.id')
                ->where('ts.name', 'accepted')
                ->select('tc.id', 'tn.full_name');

        $data = DB::table('pathway_keys as pk')
                ->leftJoinSub($tcs, 'tc', 'pk.taxonomic_scope', '=', 'tc.full_name')
                ->leftJoin('users as u1', 'pk.created_by', '=', 'u1.name')
                ->leftJoin('agents as a1', 'u1.id', '=', 'a1.user_id')
                ->leftJoin('users as u2', 'pk.updated_by', '=', 'u2.name')
                ->leftJoin('agents as a2', 'u2.id', '=', 'a2.user_id')
                ->select('pk.id', 'tc.id as taxon_concept_id', 'a1.id as created_by_id', 'a2.id as updated_by_id')
                ->get();

        foreach ($data as $row) {
            $update = (array) $row;
            $id = array_shift($update);
            DB::table('pathway_keys')->where('id', $id)->update($update);
        }

        $this->info(count($keys) . ' updated.');
        $this->info('');

        return Command::SUCCESS;
    }
}
