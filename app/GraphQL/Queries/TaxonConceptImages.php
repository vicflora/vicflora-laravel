<?php

namespace App\GraphQL\Queries;

use App\Models\Image;
use App\Models\TaxonConcept;
use App\Models\TaxonTreeItem;
use Illuminate\Database\Eloquent\Builder;

class TaxonConceptImages
{
    /**
     * @param  TaxonConcept  $taxonConcept
     */
    public function __invoke(?TaxonConcept $taxonConcept, $args)
    {
        if (!$taxonConcept && isset($args['taxonConceptId'])) {
            $taxonConcept = TaxonConcept::where('guid', $args['taxonConceptId'])->first();
        }

        $node = TaxonTreeItem::where('taxon_concept_id', $taxonConcept->id)->first();
        return Image::whereHas('acceptedConcept', function(Builder $query) use ($node) {
                $query->whereHas('taxonTreeItem', function(Builder $query) use ($node) {
                    $query->where('node_number', '>=', isset($node->node_number) ? $node->node_number : 0)
                        ->where('node_number', '<=', isset($node->highest_descendant_node_number) ? $node->highest_descendant_node_number : 0);
                });
        });
    }
}
