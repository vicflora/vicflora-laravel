<?php

namespace App\GraphQL\Queries;

use App\Models\Image;
use App\Models\TaxonConcept;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class TaxonConceptImages
{
    /**
     * @param  TaxonConcept  $taxonConcept
     */
    public function __invoke(?TaxonConcept $taxonConcept, $args): Builder
    {
        if (!$taxonConcept && isset($args['taxonConceptId'])) {
            $taxonConcept = TaxonConcept::where('guid', $args['taxonConceptId'])->first();
        }

        $query = TaxonConcept::where('id', $taxonConcept->id)
        ->union(
            TaxonConcept::select('taxon_concepts.*')
                ->join('descendants', 'descendants.id', '=', 'taxon_concepts.parent_id')
        );

        $descendants = TaxonConcept::from('descendants')
                ->withRecursiveExpression('descendants', $query);

        return Image::from('images')
                ->joinSub($descendants, 'descendants', function($join) {
                    $join->on('images.accepted_id', '=', 'descendants.id');
                })
                ->where('pixel_x_dimension', '>', 0)
                ->select('images.*');
    }
}
