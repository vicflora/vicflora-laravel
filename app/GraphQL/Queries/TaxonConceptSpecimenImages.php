<?php

namespace App\GraphQL\Queries;

use App\Models\SpecimenImage;
use App\Models\TaxonConcept;
use App\Models\TaxonTreeItem;
use Illuminate\Database\Eloquent\Builder;

class TaxonConceptSpecimenImages
{
    /**
     * @param  TaxonConcept  $taxonConcept
     */
    public function __invoke(TaxonConcept $taxonConcept)
    {
        $node = TaxonTreeItem::where('taxon_concept_id', $taxonConcept->id)->first();
        return SpecimenImage::whereHas('acceptedConcept', function(Builder $query) use ($node) {
                $query->whereHas('taxonTreeItem', function(Builder $query) use ($node) {
                        $query->where('node_number', '>=', isset($node->node_number) ? $node->node_number : 0)
                                ->where('node_number', '<=', isset($node->highest_descendant_node_number) ? $node->highest_descendant_node_number : 0);
                });
        });
    }
}
