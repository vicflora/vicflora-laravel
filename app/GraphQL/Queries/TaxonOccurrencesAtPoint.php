<?php

namespace App\GraphQL\Queries;

use App\Models\TaxonOccurrence;

class TaxonOccurrencesAtPoint
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        return TaxonOccurrence::where('accepted_name_usage_id', $args['taxonConceptId'])
                ->whereRaw("ST_Dwithin(geom, ST_GeomFromText('POINT($args[longitude] $args[latitude])', 4326), 0.08)");
    }
}
