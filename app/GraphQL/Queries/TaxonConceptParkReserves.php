<?php

namespace App\GraphQL\Queries;

use App\Models\TaxonConcept;
use App\Models\TaxonParkReserve;

class TaxonConceptParkReserves
{
    /**
     * @param \App\Models\TaxonConcept $taxonConcept
     */
    public function __invoke(TaxonConcept $taxonConcept)
    {
        return TaxonParkReserve::where('taxon_id', $taxonConcept->guid)->get();
    }
}
