<?php
// Copyright 2022 Royal Botanic Gardens Board
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

class GetPhenologyHigherTaxon {
    
    public function __invoke($taxonConceptId, $species)
    {
        return DB::table('mapper.taxon_concept_phenology_view')
                ->whereIn('taxon_concept_id', $species)
                ->select(
                    DB::raw("'$taxonConceptId' as taxon_concept_id"),
                    'month_numerical as month',
                    DB::raw('sum(total) as total'),
                    DB::raw('sum(buds) as buds'),
                    DB::raw('sum(flowers) as flowers'),
                    DB::raw('sum(fruit) as fruit')
                )
                ->groupBy('month_numerical')
                ->orderBy('month_numerical')
                ->get();
    }
}