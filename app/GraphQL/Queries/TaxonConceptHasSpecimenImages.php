<?php

namespace App\GraphQL\Queries;

use App\Actions\GetTaxonConceptSpecimenImages;
use App\Models\TaxonConcept;

class TaxonConceptHasSpecimenImages
{
    /**
     * @param  TaxonConcept|null  $taxonConcept
     * @param  array<string, mixed>  $args
     */
    public function __invoke(?TaxonConcept $taxonConcept, array $args): bool
    {
        if (!$taxonConcept && isset($args['taxonConceptId'])) {
            $taxonConcept = TaxonConcept::where('guid', $args['taxonConceptId'])
                    ->first();
        }

        $getSpecimenImages = new GetTaxonConceptSpecimenImages;
        $query = $getSpecimenImages($taxonConcept);
        return $query->exists();
    }
}
