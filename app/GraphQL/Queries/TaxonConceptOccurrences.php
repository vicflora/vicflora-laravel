<?php

namespace App\GraphQL\Queries;

use App\Models\Occurrence;
use App\Models\TaxonConcept;

class TaxonConceptOccurrences
{
    /**
     * @param  \App\Models\TaxonConcept  $taxonConcept
     */
    public function __invoke(TaxonConcept $taxonConcept)
    {
        $key = $taxonConcept->rank_id > 220 ? 'accepted_name_usage_id' : 'species_id';
        return Occurrence::where($key, $taxonConcept->guid);
    }
}
