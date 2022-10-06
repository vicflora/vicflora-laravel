<?php

namespace App\GraphQL\Mutations;

use App\Models\MultiAccessKey;

final class DeleteMultiAccessKeyCascade
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args): MultiAccessKey
    {
        $key = MultiAccessKey::where('guid', $args['id'])->first();

        if ($key->features->count()) {
            foreach ($key->features as $feature) {
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
            }
        }

        $key->delete();

        return $key;
    }
}
