<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Query;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AlaEstablishmentMeans extends Command
{
    protected $client;
    protected $source;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ala:get-establishment-means {--source=AVH}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get establishment means assertions from AVH and VBA.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->client = new Client();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->source = $this->option('source') === 'VBA' ? 'VBA' : 'AVH';
        $q = 'data_hub_uid:dh9';
        if ($this->source === 'VBA') {
            $q = 'data_resource_uid:dr1097';
        }

        $fq = [
            'state:Victoria',
            'latitude:[* TO *]',
            'longitude:[* TO *]',
            '-raw_identification_qualifier:[* TO *]',
            'kingdom:Plantae',
        ];

        $query = [
            'q' => $q,
            'fq' => $fq,
            'pageSize' => 1,
            'facets' => 'establishment_means'
        ];

        $url = 'https://biocache-ws.ala.org.au/ws/occurrences/search';
        $queryString = Query::build($query);

        $this->info($url . '?' . $queryString);

        $res = $this->client->request('GET', $url . '?' . $queryString);

        $result = json_decode($res->getBody());

        $handle = fopen(storage_path('app/ala/') . 'assertions_' . STR::lower($this->source) . '.csv', 'w');
        fputcsv($handle, ['uuid', 'catalog_number', 'establishment_means']);

        foreach ($result->facetResults[0]->fieldResult as $facet) {
            if (substr($facet->fq, 0, 1) !== '-') {
                $this->info("{$facet->label}: {$facet->count}");
                $this->getRecords($facet, $handle);
            }
        }

        fclose($handle);
    }

    protected function getRecords($facet, $handle)
    {
        $fields = [
            'id',
            'catalogNumber',
        ];

        $q = 'data_hub_uid:dh9';
        if ($this->source === 'VBA') {
            $q = 'data_resource_uid:dr1097';
        }

        $fq = [
            'state:Victoria',
            'latitude:[* TO *]',
            'longitude:[* TO *]',
            '-raw_identification_qualifier:[* TO *]',
            'kingdom:Plantae',
            $facet->fq,
        ];

        $start = 0;
        $perPage = 1000;
        $total = 1;

        while ($start < $total) {

            $query = [
                'q' => $q,
                'fq' => $fq,
                'fl' => $fields,
                'facet' => 'off',
                'pageSize' => $perPage,
                'start' => $start,
            ];

            $url = 'https://biocache-ws.ala.org.au/ws/occurrences/search';
            $queryString = Query::build($query);

            $res = $this->client->request('GET', $url . '?' . $queryString);

            $result = json_decode($res->getBody());

            $total = $result->totalRecords;
            foreach ($result->occurrences as $occ) {
                $csv = [
                    $occ->uuid,
                    $occ->raw_catalogNumber,
                    Str::lower($facet->label)
                ];
                fputcsv($handle, $csv);
            }

            $start += $perPage;
        }
    }
}
