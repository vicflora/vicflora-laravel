<?php
/**
 * Copyright 2019 Royal Botanic Gardens Victoria
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace App\Services;

use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use App\Models\Taxon;
use GuzzleHttp\Psr7\Query;
use GuzzleHttp\Utils;

use function GuzzleHttp\json_decode;
use function GuzzleHttp\Psr7\build_query;

/**
 * @author Niels Klazenga <Niels.Klazenga@rbg.vic.gov.au>
 */
class MapTaxonService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_url' => 'https://vicflora.rbg.vic.gov.au/api/'
        ]);
    }

    public function getTaxa($pageSize, $page=1)
    {
        $res = $this->client->request('GET', 'https://vicflora.rbg.vic.gov.au/api/taxa/search', [
            'query' => Query::build([
                'q' => '*:*',
                'fq' => [
                    'taxonomic_status:("accepted" OR "heterotypic synonym" OR "homotypic synonym" OR "synonym")',
                    'taxon_rank:("genus" OR "species" OR "subspecies" OR "variety" OR "forma" OR "nothovariety" OR "cultivar")',
                    'accepted_name_usage_id:[* TO *]'
                ],
                'fl' => 'id,scientific_name_id,accepted_name_usage_id,species_id,
                        parent_name_usage_id,scientific_name,scientific_name_authorship,
                        taxonomic_status,accepted_name_usage,genus,specific_epithet,
                        occurrence_status,establishment_means,taxon_rank,taxonomic_status,occurrence_status,establishment_means',
                'rows' => $pageSize,
                'page' => $page
            ])
        ]);

        $responseBody = Utils::jsonDecode($res->getBody());
        foreach ($responseBody->data as $taxon) {
            $this->loadTaxon($taxon);
        }
        return $responseBody;
    }

    protected function loadTaxon($data)
    {
        $now = date('Y-m-d H:i:s');

        $fill = [
            'id' =>$data->taxonID,
            'updated_at' => $now,
            'scientific_name_id' => $data->scientificNameID,
            'accepted_name_usage_id' => $data->acceptedNameUsageID,
            'species_id' => $this->getSpeciesId($data),
            'parent_name_usage_id' => isset($data->parentNameUsageID) ? $data->parentNameUsageID : null,
            'scientific_name' => $data->scientificName,
            'scientific_name_authorship' => isset($data->scientificNameAuthorship) ? $data->scientificNameAuthorship : null,
            'taxonomic_status' => $data->taxonomicStatus,
            'occurrence_status' => isset($data->getOccurrenceStatus) ? $this->getOccurrenceStatus($data->occurrenceStatus) : null,
            'establishment_means' => isset($data->establishmentMeans) ? $this->getEstablishmentMeans($data->establishmentMeans) : null
        ];

        $taxon = Taxon::find($data->taxonID);
        if (!$taxon) {
            $fill['id'] = $data->taxonID;
            $fill['created_at'] = $now;
            $taxon = Taxon::create($fill);
        }
        else {
            $taxon->fill($fill);
        }

        $this->updateOccurrences($taxon);
    }

    protected function getSpeciesId($data)
    {
        $speciesId = null;
        switch ($data->taxonRank) {
            case 'genus';
                $speciesId = null;
                break;
            
            case 'species':
                $speciesId = $data->taxonID;
                break;

            default:
                $speciesId = $data->parentNameUsageID;
                break;
        }
        return $speciesId;
    }

    protected function getOccurrenceStatus($status)
    {
        $ret = null;
        if ($status) {
            switch($status) {
                case in_array('endemic', $status);
                $ret = 'endemic';
                break;

                default:
                $ret = $status[0];
                break;
            }
        }
        return $ret;
    }

    protected function getEstablishmentMeans($status)
    {
        $ret = null;
        switch ($status) {
            case in_array('naturalised', $status):
                $ret = 'naturalised';
                break;
             case in_array('cultivated', $status):
                $ret = 'cultivated';
                break;
           
            default:
                $ret = $status[0];
                break;
        }
        return $ret;
    }

    protected function updateOccurrences($taxon)
    {
        $now = date('Y-m-d H:i:s');
        $matchNameService = new MatchNameService;
        DB::table('parsed_names')
                ->where('vicflora_scientific_name_id', $taxon->scientific_name_id)
                ->update([
                    'vicflora_scientific_name_id' => null,
                    'vicflora_scientific_name_id' => null,
                    'updated_at' => $now
                ]);
        $matches = $matchNameService->matchVicFloraToParsed($taxon->scientific_name_id);
        if ($matches) {
            foreach ($matches as $match) {
                DB::table('parsed_names')->where('id', $match->id)->update([
                    'vicflora_scientific_name_id' => $taxon->scientific_name_id,
                    'vicflora_taxon_id' => $taxon->id,
                    'name_match_type' => $match->match_type,
                    'updated_at' => $now
                ]);
                DB::table('occurrences')->where('parsed_name_id', $match->id)
                        ->update([
                            'taxon_id' => $taxon->id,
                            'updated_at' => $now
                        ]);
            }
        }

    }

}