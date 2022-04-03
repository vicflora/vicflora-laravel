<?php

namespace App\GraphQL\Queries;

use App\Models\TaxonConcept;

class TaxonConceptAutocomplete
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        return TaxonConcept::select('taxon_concepts.*')
                ->join('taxon_names', 'taxon_concepts.taxon_name_id', '=', 'taxon_names.id')
                ->join('taxonomic_statuses', 'taxon_concepts.taxonomic_status_id', '=', 'taxonomic_statuses.id')
                ->where('taxonomic_statuses.name', 'accepted')
                ->where('taxon_names.full_name', 'ilike', "%{$args['q']}%")
                ->orderBy('taxon_names.full_name')
                ->get();
    }
}
