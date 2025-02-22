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

use App\Models\Image;
use App\Models\TaxonConcept;
use Illuminate\Database\Eloquent\Builder;

class GetTaxonConceptImages {

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

        return Image::from('images')
                ->join('taxon_concept_image', 'images.id', '=',
                        'taxon_concept_image.image_id')
                ->join('taxon_concepts', 'taxon_concept_image.taxon_concept_id',
                        '=', 'taxon_concepts.id')
                ->joinSub($descendants, 'descendants', function($join) {
                    $join->on('taxon_concepts.accepted_id', '=',
                            'descendants.id');
                })
                ->where('pixel_x_dimension', '>', 0)
                ->select('images.*');

    }
}
