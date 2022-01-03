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
use Illuminate\Support\Collection;

/**
 * @author Niels Klazenga <Niels.Klazenga@rbg.vic.gov.au>
 */
class MatchNameService
{
    /**
     * Undocumented function
     *
     * @param int $parsedNameId
     * @return object|null
     */
    public static function matchParsedToVicFlora($parsedNameId)
    {
        $name = DB::table('parsed_names')->where('id', $parsedNameId)
                ->select('scientific_name', 'canonical_name_complete', 
                        'canonical_name_with_marker')
                ->first();
        $selectString = "id as taxon_id, scientific_name_id, CASE 
                WHEN scientific_name || ' ' || scientific_name_authorship=? THEN 'exactMatch'
                WHEN scientific_name || ' ' || scientific_name_authorship=? THEN 'exactMatch'
                WHEN scientific_name=? THEN 'canonicalNameMatch' END as match_type";
        $match = DB::table('taxa')->orWhere('scientific_name', $name->scientific_name)
                ->orWhere('scientific_name', $name->canonical_name_complete)
                ->orWhere('scientific_name', $name->canonical_name_with_marker)
                ->selectRaw($selectString, array_values((array) $name))->first();
        return $match;
    }

    /**
     * @param string $scientificNameID
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function matchVicFloraToParsed($scientificNameID) :Collection
    {
        $name = DB::table('taxa')
                ->where('scientific_name_id', $scientificNameID)
                ->select('scientific_name', DB::raw("concat(scientific_name || ' ' || scientific_name_authorship) as scientific_name_with_author"))
                ->first();
        $selectString = "id, CASE
                WHEN scientific_name=? THEN 'exactMatch'
                WHEN canonical_name_complete=? THEN 'exactMatch'
                WHEN canonical_name_with_marker=? THEN 'canonicalNameMatch'
                END as match_type";
        $matches = DB::table('parsed_names')
                ->orWhere('scientific_name', $name->scientific_name_with_author)
                ->orWhere('canonical_name_complete', $name->scientific_name_with_author)
                ->orWhere('canonical_name_with_marker', $name->scientific_name)
                ->selectRaw($selectString, [
                    $name->scientific_name_with_author, 
                    $name->scientific_name_with_author, 
                    $name->scientific_name
                ])->get();
        return $matches;
    }
}