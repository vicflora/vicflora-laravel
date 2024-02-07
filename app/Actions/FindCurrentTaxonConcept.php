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

use Illuminate\Support\Facades\DB;

class FindCurrentTaxonConcept {
    
    /**
     * Find current taxon concept for name
     *
     * @param string $name
     * @return array|null
     */
    public function __invoke(string $name): ?array
    {

        $taxon = DB::table('taxon_names as tn')
            ->join('taxon_concepts as tc', 'tn.id', '=', 'tc.taxon_name_id')
            ->join('taxonomic_statuses as ts', 'tc.taxonomic_status_id', '=', 'ts.id')
            ->leftJoin('taxon_concepts as tc2', 'tc.accepted_id', '=', 'tc2.id')
            ->leftJoin('taxon_names as tn2', 'tc2.taxon_name_id', '=', 'tn2.id')
            ->whereIn('ts.name', ['accepted', 'synonym', 'heterotypicSynonym', 'homotypicSynonym'])
            ->where('tn.full_name', $name)
            ->select('tc2.id', 'tn2.full_name', DB::raw('case when tc.guid != tc2.guid then tn.full_name else null end as "as"'))
            ->first();

        return $taxon ? (array) $taxon : null;
    }
}