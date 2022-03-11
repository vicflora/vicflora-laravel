<?php

namespace App\GraphQL\Queries;

use App\Models\TaxonConcept;

class TaxonConceptHigherClassification
{
    /**
     * @param  TaxonConcept|null  $taxonConcept
     * @param  array<string, mixed>  $args
     */
    public function __invoke(?TaxonConcept $taxonConcept, array $args)
    {
        if (!$taxonConcept && isset($args['taxonConceptId'])) {
            $taxonConcept = TaxonConcept::where('guid', $args['taxonConceptId'])->first();
        }

        if ($taxonConcept->taxonomicStatus->name === 'accepted') {
            $query = TaxonConcept::where('id', $taxonConcept->parent_id)
            ->union(
                TaxonConcept::select('taxon_concepts.*')
                    ->join('tree', 'tree.parent_id', '=', 'taxon_concepts.id')
            );
    
            $tree = TaxonConcept::from('tree')
                    ->withRecursiveExpression('tree', $query)
                    ->get();

            $allowedRanks = [
                'kingdom',
                'phylum',
                'class',
                'order',
                'family',
                'genus',
                'species',
            ];

            return $tree->filter(function($element) use ($allowedRanks) {
                        return in_array($element->taxonTreeDefItem->name, $allowedRanks);
                    })->sortBy([
                        fn ($a, $b) 
                            => $a->taxonTreeDefItem->rank_id <=> $b->taxonTreeDefItem->rank_id,
                    ]);
        }
        return null;
    }
}
