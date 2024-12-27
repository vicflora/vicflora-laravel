<?php

namespace App\GraphQL\Queries;

use App\Models\TaxonConcept;
use App\Services\CantoImageService;

class TaxonConceptHeroImage
{
    /**
     * @param \App\Models\TaxonConcept $taxonConcept
     * @param array $args
     * @return array|null
     */
    public function __invoke(TaxonConcept $taxonConcept, array $args): ?array
    {
        $imageService = new CantoImageService();

        return $imageService->heroImage($taxonConcept->guid);
    }
}
