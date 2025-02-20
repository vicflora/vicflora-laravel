<?php

namespace App\GraphQL\Queries;

use App\Actions\GetTaxonConceptImages;
use App\Models\TaxonConcept;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class TaxonConceptImages
{
    /**
     * @param  TaxonConcept  $taxonConcept
     */
    public function __invoke(?TaxonConcept $taxonConcept, $args): Builder
    {
        if (!$taxonConcept && isset($args['taxonConceptId'])) {
            $taxonConcept = TaxonConcept::where('guid',
                    $args['taxonConceptId'])->first();
        }

        $getImages = new GetTaxonConceptImages;
        $query = $getImages($taxonConcept);
        $query->orderBy('images.hero_image', 'desc')
            ->orderBy('images.rating', 'desc')
            ->orderBy(DB::raw('random()'));
        return $query;
    }
}
