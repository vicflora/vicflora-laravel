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

use App\Models\BioregionOccurrence;
use App\Models\LocalGovernmentAreaOccurrence;
use App\Models\Occurrence;
use App\Models\ParkReserveOccurrence;
use Illuminate\Support\Facades\DB;
use MStaack\LaravelPostgis\Geometries\Point;

class MapOccurrencesService
{
    public function loadOccurrence($data)
    {
        $parseNameService = new ParseNameService;
        $now = date('Y-m-d H:i:s');
        $name = $parseNameService->parseName($data->raw_scientificName);
        $fill = [
            'uuid' => $data->id,
            'updated_at' => $now,
            'data_resource_uid' => $data->dataResourceUid,
            'institution_code' => $data->institutionCode,
            'collection_code' => $data->collectionCode,
            'catalog_number' => $data->catalogNumber,
            'scientific_name' => $data->scientificName ?: null,
            'unprocessed_scientific_name' => $data->raw_scientificName,
            'parsed_name_id' => $name,
            'latitude' => $data->decimalLatitude,
            'longitude' => $data->decimalLongitude,
            'data_source_id' => $data->dataResourceUid === 'dr1097' ? 2 : 1,
        ];

        $occurrence = Occurrence::firstOrNew(
            ['uuid' => $data->id]
        );
        $occurrence->fill($fill);
        $point = new Point($data->decimalLatitude, $data->decimalLongitude);
        $occurrence->geom = $point;
        $occurrence->save();
    }

    // public function getBioregion($uuid)
    // {
    //     $bioregion = DB::table('bioregions')
    //         ->join('occurrences', function($join) {
    //             $join->whereRaw('ST_Intersects(bioregions.geom, occurrences.geom)');
    //         })
    //         ->where('occurrences.uuid', $uuid)
    //         ->first();

    //     if ($bioregion) {
    //         BioregionOccurrence::create([
    //             'bioregion_id' => $bioregion->id,
    //             'occurrence_id' => $uuid
    //         ]);
    //     }
    // }

    /**
     * @param string $uuid
     * @return void
     */
    // public function getLocalGovernmentArea($uuid)
    // {
    //     $bioregion = DB::table('local_government_areas')
    //         ->join('occurrences', function($join) {
    //             $join->whereRaw('ST_Intersects(local_government_areas.geom, occurrences.geom)');
    //         })
    //         ->where('occurrences.uuid', $uuid)
    //         ->first();

    //     if ($bioregion) {
    //         LocalGovernmentAreaOccurrence::create([
    //             'local_government_area_id' => $bioregion->id,
    //             'occurrence_id' => $uuid
    //         ]);
    //     }
    // }

    /**
     * @param string $uuid
     * @return void
     */
    // public function getParkReserves($uuid)
    // {
    //     $parks = DB::table('park_reserves')
    //         ->join('occurrences', function($join) {
    //             $join->whereRaw('ST_Intersects(park_reserves.geom, occurrences.geom)');
    //         })
    //         ->where('occurrences.uuid', $uuid)
    //         ->get();

    //     if ($parks->count()) {
    //         foreach ($parks as $park) {
    //             ParkReserveOccurrence::create([
    //                 'park_reserve_id' => $park->id,
    //                 'occurrence_id' => $uuid
    //             ]);
    //         }
    //     }
    // }
}

