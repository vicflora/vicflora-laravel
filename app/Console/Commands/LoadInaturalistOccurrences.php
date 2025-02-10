<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LoadInaturalistOccurrences extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inaturalist:load-data {--group=} {--recreate-tables}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load iNaturalist occurrence data into database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        if ($this->option('recreate-tables')) {
            $this->call('inaturalist:recreate-tables');
        }

        $this->info('Get and load iNaturalist data');

        $client = new Client(['base_uri' => 'https://api.inaturalist.org/v1/']);

        $params = [
            'quality_grade' => 'research',
            'place_id' => 6744,              // Australia
            'per_page' => 200,
            'rank' => 'species,subspecies,variety,form',
            'photo_license' => 'cc-by,cc-by-nc,cc-by-nd,cc-by-sa,cc-by-nc-nd,cc-by-nc-sa,cc0',
        ];

        $groups = [
            'fungi' => 47170,
            'mycetozoa' => 47685
        ];

        $params['taxon_id'] = $groups[$this->option('group')];

        $moreData = true;

        while ($moreData) {
            try {
                $response = $client->request('GET', 'observations', [
                    'query' => $params
                ]);

                $data = json_decode($response->getBody()->getContents(), true);

                $results = $data['results'];

                if (count($results) < $params['per_page']) {
                    $moreData = false;
                }

                $observations = [];
                $photos = [];
                $observationPhotos = [];
                $taxonIds = [];
                $taxa = [];
                $userIds = [];
                $users = [];

                foreach ($results as $index => $result) {
                    if ($index == $params['per_page'] - 1) {
                        $params['id_below'] = $result['id'];
                    }

                    list($latitude, $longitude) = explode(',', $result['location']);

                    $observations[] = [
                        'id' => $result['id'],
                        'uuid' => $result['uuid'],
                        'quality_grade' => $result['quality_grade'],
                        'site_id' => $result['site_id'],
                        'license_code' => $result['license_code'],
                        'created_at' => $result['created_at'],
                        'description' => $result['description'],
                        'observed_on' => $result['observed_on'],
                        'observed_on_string' => $result['observed_on_string'],
                        'observed_on_details' => json_encode($result['observed_on_details']),
                        'updated_at' => $result['updated_at'],
                        'place_ids' => json_encode($result['place_ids']),
                        'taxon_id' => $result['taxon']['id'],
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                        'geojson' => json_encode($result['geojson']),
                        'place_guess' => $result['place_guess'],
                        'user_id' => $result['user']['id'],
                    ];

                    foreach ($result['observation_photos'] as $photo) {
                        if ($photo['photo']['license_code']) {
                            $photos[] = [
                                'id' => $photo['photo_id'],
                                'license_code' => $photo['photo']['license_code'],
                                'original_dimensions' => json_encode($photo['photo']['original_dimensions']),
                                'attribution' => $photo['photo']['attribution'],
                                'url' => $photo['photo']['url'],
                            ];

                            $observationPhotos[] = [
                                'id' => $photo['id'],
                                'position' => $photo['position'],
                                'uuid' => $photo['uuid'],
                                'photo_id' => $photo['photo_id'],
                                'observation_id' => $result['id'],
                            ];
                        }
                    }

                    if (!in_array($result['taxon']['id'], $taxonIds)) {
                        $taxonIds[] = $result['taxon']['id'];

                        $taxa[] = [
                            'id' => $result['taxon']['id'],
                            'name' => $result['taxon']['name'],
                            'rank' => $result['taxon']['rank'],
                            'rank_level' => $result['taxon']['rank_level'],
                            'parent_id' => $result['taxon']['parent_id'],
                            'ancestor_ids' => json_encode($result['taxon']['ancestor_ids']),
                        ];
                    }

                    if (!in_array($result['user']['id'], $userIds)) {
                        $userIds[] = $result['user']['id'];

                        $users[] = [
                            'id' => $result['user']['id'],
                            'login' => $result['user']['login'],
                            'name' => $result['user']['name'],
                            'orcid' => $result['user']['orcid'],
                        ];
                    }
                }

                DB::table('inaturalist.taxa')->insertOrIgnore($taxa);
                DB::table('inaturalist.users')->insertOrIgnore($users);
                DB::table('inaturalist.observations')->insertOrIgnore($observations);
                DB::table('inaturalist.photos')->insertOrIgnore($photos);
                DB::table('inaturalist.observation_photos')->insertOrIgnore($observationPhotos);

            }
            catch (TransferException $e) {
                echo $e->getMessage();
            }
        }


        $this->info('Fix names of infraspecific taxa');

        $names = DB::table('inaturalist.taxa as t')
            ->whereIn('rank', ['subspecies', 'variety', 'form'])
            ->select('id', 'name','rank')
            ->get();

        foreach ($names as $name) {
            list ($genericName, $firstEpithet, $secondEpithet) = explode(' ', $name->name);

            switch ($name->rank) {
                case 'subspecies':
                    $newName = "$genericName $firstEpithet subsp. $secondEpithet";
                    break;

                case 'variety':
                    $newName = "$genericName $firstEpithet var. $secondEpithet";
                    break;

                case 'form':
                    $newName = "$genericName $firstEpithet f. $secondEpithet";
                    break;

                default:
                    $newName = $name->name;
                    break;
            }

            DB::table('inaturalist.taxa')->where('id', $name->id)->update(['name' => $newName]);
        }


        $this->info('Get higher taxa');

        $numParents = 1;
        $iteration = 0;

        while ($numParents > 0) {
            $parents = DB::table('inaturalist.taxa as t')
                ->leftJoin('inaturalist.taxa as t2', 't.parent_id', '=', 't2.id')
                ->whereNull('t2.id')
                ->where('t.name', '!=', 'Life')
                ->distinct()
                ->pluck('t.parent_id');

            $numParents = count($parents);
            $iteration++;
            echo $iteration . ': ' . $numParents . "\n";

            if ($numParents) {
                $chunks = collect($parents)->chunk(30);
                foreach ($chunks as $chunk) {
                    $ids = implode(',', $chunk->toArray());
                    try {
                        $response = $client->request('GET', 'taxa/' . $ids);
                        $data = json_decode($response->getBody()->getContents(), true);

                        $taxa = [];
                        foreach ($data['results'] as $result) {
                            $taxa[] = [
                                'id' => $result['id'],
                                'name' => $result['name'],
                                'rank' => $result['rank'],
                                'rank_level' => $result['rank_level'],
                                'parent_id' => $result['parent_id'],
                                'ancestor_ids' => json_encode($result['ancestor_ids']),
                            ];
                        }

                        DB::table('inaturalist.taxa')->insertOrIgnore($taxa);
                    }
                    catch(TransferException $e) {
                        echo $e->getMessage();
                    }
                }
            }
        }


        $this->info('Match licenses');

        $sql = <<<SQL
update inaturalist.photos 
set license_id = licenses.id
from licenses 
where photos.license_code = licenses.name
SQL;
        DB::update($sql);


        $this->info('Link iNaturalist photos to taxon concepts');

        $rels = DB::table('inaturalist.photos as p')
            ->join('inaturalist.observation_photos as op', 'p.id', '=', 'op.photo_id')
            ->join('inaturalist.observations as o', 'op.observation_id', '=', 'o.id')
            ->join('inaturalist.taxa as t', 'o.taxon_id', '=', 't.id')
            ->join('taxon_names as tn', 't.name', '=', 'tn.full_name')
            ->join('taxon_concepts as tc', 'tn.id', '=', 'tc.taxon_name_id')
            ->select('tc.id as taxon_concept_id', 'p.id as inaturalist_photo_id')
            ->get();

        $insertData = $rels->map(fn ($rel) => (array) $rel);
        $chunks = collect($insertData)->chunk(1000);
        foreach ($chunks as $chunk) {
            DB::table('taxon_concept_inaturalist_photo')->insertOrIgnore($chunk->toArray());
        }


        return 0;
    }
}
