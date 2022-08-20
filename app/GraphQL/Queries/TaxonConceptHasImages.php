<?php

namespace App\GraphQL\Queries;

use App\Actions\GetTaxonConceptImages;
use App\Models\TaxonConcept;

class TaxonConceptHasImages
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke(?TaxonConcept $taxonConcept, array $args)
    {
        if (!$taxonConcept && isset($args['taxonConceptId'])) {
            $taxonConcept = TaxonConcept::where('guid', $args['taxonConceptId'])
                    ->first();
        }

        $getImages = new GetTaxonConceptImages;
        $query = $getImages($taxonConcept);
        return $query->exists();
    }
}
