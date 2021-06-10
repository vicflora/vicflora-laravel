<?php

namespace App\GraphQL\Queries;

use App\Models\TaxonOccurrence;

class TaxonOccurrences
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        return TaxonOccurrence::where('taxon_concept_id', $args['taxonConceptId']);
    }
}
