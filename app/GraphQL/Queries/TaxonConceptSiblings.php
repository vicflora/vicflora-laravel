<?php

namespace App\GraphQL\Queries;

use App\Models\TaxonConcept;

class TaxonConceptSiblings
{
    /**
     * @param  TaxonConcept|null  $taxonConcept
     * @param  array<string, mixed>  $args
     */
    public function __invoke(?TaxonConcept $taxonConcept, array $args)
    {
        if (!$taxonConcept && isset($args['taxonConceptId'])) {
            $taxonConcept = TaxonConcept::where('guid', $args['taxonConceptId'])->first();
        }

        return $taxonConcept->siblings;
    }
}
