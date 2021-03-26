<?php

namespace App\GraphQL\Queries;

use GuzzleHttp\Client;

class TaxonConceptKeys
{
    /**
     * @param  \App\Models\TaxonConcept  $taxonConcept
     * @param  array<string, mixed>  $args
     */
    public function __invoke($taxonConcept, array $args)
    {
        $client = new Client(['base_uri' => 'https://data.rbg.vic.gov.au']);
        $res = $client->request('GET', '/keybase-ws/ws/search_items/'. $taxonConcept->taxonName->full_name, [
            'query' => [
                'project' => 10,
            ]
        ]);
        
        return collect(json_decode($res->getBody()) ?: []);
    }
}
