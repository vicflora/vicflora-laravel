<?php

namespace App\GraphQL\Queries;

use App\Models\TaxonBioregion;
use App\Models\TaxonConcept;

class TaxonConceptBioregions
{
    /**
     * @param \App\Models\TaxonConcept $taxonConcept
     */
    public function __invoke(TaxonConcept $taxonConcept)
    {
        return TaxonBioregion::where('taxon_guid', $taxonConcept->guid)->get();
    }
}
