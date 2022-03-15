<?php

namespace App\GraphQL\Queries;

use App\Models\TaxonConcept;
use App\Models\TaxonConceptFloraLink;
use Illuminate\Database\Eloquent\Collection;

class TaxonConceptFloraLinks
{
    /**
     * @param  TaxonConcept $taxonConcept
     * @param  array<string, mixed>  $args
     */
    public function __invoke(TaxonConcept $taxonConcept, array $args): Collection
    {
        return TaxonConceptFloraLink::select('taxon_concept_flora_links.*')
                ->join('floras', 'taxon_concept_flora_links.flora_id', '=', 
                        'floras.id')
                ->where('taxon_concept_flora_links.taxon_concept_id', $taxonConcept->id)
                ->orderBy('floras.sort_order')
                ->get();
    }
}
