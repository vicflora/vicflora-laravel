<?php

namespace App\GraphQL\Queries;

use App\Actions\GetTaxonConceptSpecimenImages;
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
            $taxonConcept = TaxonConcept::where('guid', $args['taxonConceptId'])
                    ->first();
        }

        $getSpecimenImages = new GetTaxonConceptSpecimenImages;
        $query = $getSpecimenImages($taxonConcept);
        return $query->orderBy('specimen_images.cumulus_record_id');
    }
}
