<?php

namespace App\GraphQL\Queries;

final class CatchmentsByPoint
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $point = "ST_GeomFromText('POINT($args[longitude] $args[latitude])', 4326)";

        return \App\Models\Catchment::whereRaw("ST_Intersects(geom, $point)")->get();
    }
}
