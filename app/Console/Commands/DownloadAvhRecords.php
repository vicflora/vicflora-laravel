<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use GuzzleHttp\Psr7\Query;
use Illuminate\Support\Facades\Storage;
use ZanySoft\Zip\Zip;

class DownLoadAvhRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ala:download-avh-records';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Downloads AVH occurrence records from ALA';

    /**
     * GuzzleHTTP client
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

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
     * @return mixed
     */
    public function handle()
    {

        $fields = [
            'id',
            'data_resource_uid',
            'institution_code',
            'collection_code',
            'catalogue_number',
            'taxon_concept_lsid',
            'genus_guid',
            'species_guid',
            'rank',
            'genus',
            'species',
            'raw_taxon_name',
            'taxon_name',
            'latitude',
            'longitude',
            'coordinate_precision',
            'raw_coordinate_precision',
            'coordinate_uncertainty',
            'kingdom',
            'phylum',
            'class',
            'order',
            'family'
        ];

        $fq = [
            'state:Victoria',
            'latitude:[* TO *]',
            'longitude:[* TO *]',
            '-raw_identification_qualifier:[* TO *]',
            'kingdom:Plantae',
        ];

        $query = [
            'q' => 'data_hub_uid:dh9 OR data_resource_uid:dr1097',
            'fq' => $fq,
            'fields' => implode(',', $fields),
            'qa' => 'none',
            'email' => 'Niels.Klazenga@rbg.vic.gov.au',
            'reasonType' => 4,
        ];

        $queryString = Query::build($query);

        $url = 'https://biocache-ws.ala.org.au/ws/occurrences/offline/download';

        $res = $this->client->request('GET', $url . '?' . $queryString);

        $result = json_decode($res->getBody());

        $this->getDownload($result->statusUrl);

    }

    /**
     * @param string $statusUrl
     * @return string
     */
    protected function getDownload($statusUrl)
    {
        $res = $this->client->request('GET', $statusUrl);
        $body = $res->getBody();
        $this->info($body);
        $json = json_decode($body);
        if (isset($json->downloadUrl)) {
            $this->info('Download URL: ' . $json->downloadUrl);
            $contents = file_get_contents($json->downloadUrl);
            Storage::put('ala/avh_data.zip', $contents);
            $this->info("File downloaded and stored as '/storage/app/ala/avh_data.zip'");
            $zip = Zip::open(storage_path('app/ala') . '/avh_data.zip');
            $zip->extract(storage_path('app/ala/avh_data'));
            $this->info("Files extracted to '/storage/app/ala/avh_data'");
        }
        else {
            sleep(60);
            $this->getDownload($statusUrl);
        }
    }
}
