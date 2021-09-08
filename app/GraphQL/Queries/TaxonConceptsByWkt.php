<?php

namespace App\GraphQL\Queries;

use App\Models\TaxonConcept;
use Illuminate\Database\Eloquent\Builder;

class TaxonConceptsByWkt
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        return TaxonConcept::whereHas('occurrences', function(Builder $query) use ($args) {
            $query->whereRaw("ST_Intersects(geom, ST_GeomFromText('{$args['wkt']}', 4326))");
        });
    }
}
