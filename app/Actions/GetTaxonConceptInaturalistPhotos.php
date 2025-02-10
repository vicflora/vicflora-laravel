<?php
// Copyright 2025 Royal Botanic Gardens Board
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

use App\Models\InaturalistPhoto;
use App\Models\TaxonConcept;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class GetTaxonConceptInaturalistPhotos {

    public function __invoke(TaxonConcept $taxonConcept): Builder
    {
        $query = TaxonConcept::where('id', $taxonConcept->id)
        ->union(
            TaxonConcept::select('taxon_concepts.*')
                ->join('descendants', 'descendants.id', '=',
                        'taxon_concepts.parent_id')
        );

        $descendants = TaxonConcept::from('descendants')
                ->withRecursiveExpression('descendants', $query);

        return InaturalistPhoto::from('inaturalist.photos as p')
            ->join('taxon_concept_inaturalist_photo as tcip', 'p.id', '=', 'tcip.inaturalist_photo_id')
            ->join('taxon_concepts as tc', 'tcip.taxon_concept_id', '=', 'tc.id')
            ->joinSub($descendants, 'd', function ($join) {
                $join->on(DB::raw('coalesce(tc.accepted_id, tc.id)'), '=', 'd.id');
            })
            ->select('p.*');
    }
}
