<?php

namespace App\GraphQL\Queries;

use App\Models\Image;
use App\Models\TaxonConcept;
use Illuminate\Support\Facades\DB;

class TaxonConceptHeroImage
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke(TaxonConcept $taxonConcept, array $args)
    {
        $query = TaxonConcept::where('id', $taxonConcept->id)
        ->union(
            TaxonConcept::select('taxon_concepts.*')
                ->join('descendants', 'descendants.id', '=', 'taxon_concepts.parent_id')
        );

        $descendants = TaxonConcept::from('descendants')
                ->withRecursiveExpression('descendants', $query);

        return Image::from('images')
                ->joinSub($descendants, 'descendants', function($join) {
                    $join->on('images.accepted_id', '=', 'descendants.id');
                })
                ->where('pixel_x_dimension', '>', 0)
                ->orderBy('hero_image', 'desc')
                ->orderBy('subtype', 'desc')
                ->orderBy('rating', 'desc')
                ->orderBy(DB::raw('random()'))
                ->first();
    }
}
