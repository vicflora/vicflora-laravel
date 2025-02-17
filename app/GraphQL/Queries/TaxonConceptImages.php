<?php

namespace App\GraphQL\Queries;

use App\Actions\GetTaxonConceptImages;
use App\Models\TaxonConcept;
use Illuminate\Database\Eloquent\Builder;

class TaxonConceptImages
{
    /**
     * @param  TaxonConcept  $taxonConcept
     */
    public function __invoke(?TaxonConcept $taxonConcept, $args): Builder
    {
        if (!$taxonConcept && isset($args['taxonConceptId'])) {
            $taxonConcept = TaxonConcept::where('guid',
                    $args['taxonConceptId'])->first();
        }

        $getImages = new GetTaxonConceptImages;
        $query = $getImages($taxonConcept);
        return $query;
    }
}
