<?php

namespace App\GraphQL\Queries;

use App\Models\Bioregion;

class BioregionsByPoint
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $point = "ST_GeomFromText('POINT($args[longitude] $args[latitude])', 4326)";

        return Bioregion::whereRaw("ST_Intersects(geom, $point)")->get();
    }
}
