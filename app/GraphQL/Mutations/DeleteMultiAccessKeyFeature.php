<?php

namespace App\GraphQL\Mutations;

use App\Models\MultiAccessKeyFeature;

final class DeleteMultiAccessKeyFeature
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args): MultiAccessKeyFeature
    {
        $feature = MultiAccessKeyFeature::where('guid', $args['id'])->first();

        $feature->delete();

        return $feature;
    }
}
