<?php

namespace App\GraphQL\Queries;

use App\Actions\GetTaxonConceptInaturalistPhotos;
use App\Models\TaxonConcept;

final class TaxonConceptHasInaturalistPhotos
{
    /**
     * @param  TaxonConcept|null $taxonConcept
     * @param  array{}  $args
     */
    public function __invoke(?TaxonConcept $taxonConcept, array $args): bool
    {
        if (!$taxonConcept && isset($args['taxonConceptId'])) {
            $taxonConcept = TaxonConcept::where('guid', $args['taxonConceptId'])
                    ->first();
        }

        $getInaturalistPhotos = new GetTaxonConceptInaturalistPhotos;
        $query = $getInaturalistPhotos($taxonConcept);
        return $query->exists();
    }
}
