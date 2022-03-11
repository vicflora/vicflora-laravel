<?php

namespace App\GraphQL\Queries;

use App\Models\SpecimenImage;
use App\Models\TaxonConcept;
use Illuminate\Database\Eloquent\Builder;

class TaxonConceptSpecimenImages
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

        return SpecimenImage::from('specimen_images')
                ->joinSub($descendants, 'descendants', function($join) {
                    $join->on('specimen_images.accepted_id', '=', 'descendants.id');
                });
    }
}
