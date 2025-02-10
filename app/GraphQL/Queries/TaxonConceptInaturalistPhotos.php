<?php

namespace App\GraphQL\Queries;

use App\Actions\GetTaxonConceptInaturalistPhotos;
use App\Models\TaxonConcept;
use Illuminate\Database\Eloquent\Builder;

final class TaxonConceptInaturalistPhotos
{
    /**
     * @param  TaxonConcept  $taxonConcept
     * @param  array{}  $args
     */
    public function __invoke(?TaxonConcept $taxonConcept, array $args): Builder
    {
        if (!$taxonConcept && isset($args['taxonConceptId'])) {
            $taxonConcept = TaxonConcept::where('guid', $args['taxonConceptId'])
                    ->first();
        }

        $getInaturalistPhotos = new GetTaxonConceptInaturalistPhotos;
        $query = $getInaturalistPhotos($taxonConcept);
        return $query->with(['license', 
                'observations.user', 
                'taxonConcepts.taxonName', 
                'taxonConcepts.acceptedConcept.taxonName'])
            ->orderBy('p.id', 'desc');
    }
}
