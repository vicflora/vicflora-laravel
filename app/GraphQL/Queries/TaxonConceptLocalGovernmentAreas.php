<?php

namespace App\GraphQL\Queries;

use App\Models\TaxonConcept;
use App\Models\TaxonLocalGovernmentArea;

class TaxonConceptLocalGovernmentAreas
{
    /**
     * @param \App\Models\TaxonConcept $taxonConcept
     */
    public function __invoke(TaxonConcept $taxonConcept)
    {
        return TaxonLocalGovernmentArea::where('taxon_id', $taxonConcept->guid)->get();
    }
}
