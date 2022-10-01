<?php

namespace App\GraphQL\Mutations;

use App\Models\MultiAccessKeyFeature;

final class DeleteMultiAccessKeyFeatureCascade
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args): MultiAccessKeyFeature
    {
        $feature = MultiAccessKeyFeature::where('guid', $args['id'])->first();

        if ($feature->states->count()) {
            foreach ($feature->states as $state) {
                $state->delete();
            }
        }

        if ($feature->children->count()) {
            foreach ($feature->children as $child) {
                $child->delete();
            }
        }

        $feature->delete();

        return $feature;
    }
}
