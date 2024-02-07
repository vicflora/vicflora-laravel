<?php
// Copyright 2024 Royal Botanic Gardens Board
// 
// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
// 
//     http://www.apache.org/licenses/LICENSE-2.0
// 
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.

namespace App\Actions;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GetConservationList
{

    public function __invoke(string $code): Collection
    {

        $list = DB::table('taxon_concepts as tc')
            ->join('taxon_names as tn', 'tc.taxon_name_id', '=', 'tn.id')
            ->join(
                'taxon_concept_threat_statuses as tcts',
                'tc.id',
                '=',
                'tcts.taxon_concept_id'
            )
            ->join(
                'conservation_lists as cl',
                'tcts.conservation_list_id',
                '=',
                'cl.id'
            )
            ->join(
                'iucn_categories as ic',
                'tcts.iucn_category_id',
                '=',
                'ic.id'
            )
            ->where('cl.code', $code)
            ->select(
                'tc.guid as taxon_concept_id',
                'tn.full_name as scientific_name',
                'ic.name as iucn_category',
                'ic.code as iucn_code',
                'tcts.as'
            )
            ->get();

        return $list;
    }
}
