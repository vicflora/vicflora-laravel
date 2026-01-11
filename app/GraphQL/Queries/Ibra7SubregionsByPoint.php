<?php

namespace App\GraphQL\Queries;

use App\Models\Ibra7Subregion;

final class Ibra7SubregionsByPoint
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $point = "ST_GeomFromText('POINT($args[longitude] $args[latitude])', 4326)";

        return Ibra7Subregion::whereRaw("ST_Intersects(geom, $point)")->get();
    }
}
