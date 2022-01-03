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

/**
 * ParseNameService
 *
 * @author Niels Klazenga <Niels.Klazenga@rbg.vic.gov.au>
 */
class ParseNameService {
    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * @param string $name
     * @return integer
     */
    public function parseName($name)
    {
        $parsed_name = $this->getParsedNameFromDatabase($name);
        if (!$parsed_name) {
            // $parsed_name_gbif = $this->getParsedNameFromService($name);

            // if (isset($parsed_name_gbif->canonicalName)) {
            //     $matched_name = $this->matchParsedName($parsed_name_gbif);
            //     $parsed_name = $this->storeParsedName($parsed_name_gbif, $matched_name);
            // }
            $parsed_name = $this->insertUnparsedName($name);
        }
        return $parsed_name;
    }

    /**
     * Checks if the parsed name is already in the database
     *
     * @param string $name
     * @return object
     */
    public function getParsedNameFromDatabase($name)
    {
        return DB::table('mapper.parsed_names')->where('scientific_name', $name)
                ->value('id');
    }

    public function insertUnparsedName($name) {
        return DB::table('mapper.parsed_names')->insertGetId(
            [
                'scientific_name' => $name,
                'type' => 'UNPARSED'
            ]
            );
    }

    /**
     * Use the GBIF name parsing service to parse the name
     *
     * @param string $name
     * @return object
     */
    public function getParsedNameFromService($name) {

        $res = $this->client->request('GET', 'https://api.gbif.org/v1/parser/name', [
            'query' => [
                'name' => $name
            ]
        ]);
        $body = json_decode($res->getBody());
        if (count($body)) {
            return $body[0];
        }
        return false;
    }

    /**
     * Match the name or canonical name to a name in VicFlora
     *
     * @param object $parsed_name
     * @return object|nullphp artisan
     */
    public function matchParsedName($parsed_name)
    {
        $name = (object) [
            'scientific_name' => $parsed_name->scientificName,
            'canonical_name_complete' => $parsed_name->canonicalNameComplete,
            'canonical_name_with_marker' => $parsed_name->canonicalNameWithMarker
        ];

        $selectString = "id as taxon_id, scientific_name_id, CASE
                WHEN scientific_name || ' ' || scientific_name_authorship=? THEN 'exactMatch'
                WHEN scientific_name || ' ' || scientific_name_authorship=? THEN 'exactMatch'
                WHEN scientific_name=? THEN 'canonicalNameMatch' END as match_type";

        return DB::table('mapper.taxa')->orWhere('scientific_name', $name->scientific_name)
                ->orWhere('scientific_name', $name->canonical_name_complete)
                ->orWhere('scientific_name', $name->canonical_name_with_marker)
                ->selectRaw($selectString, array_values((array) $name))->first();
    }

    /**
     * Store the parsed name
     *
     * @param object $parsed_name
     * @param object|boolean $matched_name
     * @return integer
     */
    public function storeParsedName($parsed_name, $matched_name=false)
    {
        $now = date('Y-m-d H:i:s');

        $insert = [
            'created_at' => $now,
            'updated_at' => $now,
            'scientific_name' => $parsed_name->scientificName,
            'type' => $parsed_name->type,
            'genus_or_above' => isset($parsed_name->genusOrAbove) ? $parsed_name->genusOrAbove : null,
            'specific_epithet' => isset($parsed_name->specificEpithet) ? $parsed_name->specificEpithet: null,
            'authorship' => isset($parsed_name->authorship) ? $parsed_name->authorship : null,
            'bracket_authorship' => isset($parsed_name->bracketAuthorship) ? $parsed_name->bracketAuthorship : null,
            'authors_parsed' => (bool) $parsed_name->parsed,
            'canonical_name' => isset($parsed_name->canonicalName) ? $parsed_name->canonicalName : null,
            'canonical_name_with_marker' => isset($parsed_name->canonicalNameWithMarker) ? $parsed_name->canonicalNameWithMarker : null,
            'canonical_name_complete' => isset($parsed_name->canonicalNameComplete) ? $parsed_name->canonicalNameComplete : null,
            'rank_marker' => isset($parsed_name->rankMarker) ? $parsed_name->rankMarker : null,
        ];

        if ($matched_name) {
            $insert['vicflora_scientific_name_id'] = $matched_name->scientific_name_id;
            $insert['name_match_type'] = $matched_name->match_type;
        };

        return DB::table('mapper.parsed_names')->insertGetId($insert);
    }
}
