<?php

namespace App\GraphQL\Queries;

use App\Models\Reference;

class ReferenceQuickRef
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke(Reference $reference, array $args): ?string
    {
        $contributors = $reference->contributors->sortBy('sequence')->map(function($item) {
            return $item->agent->last_name;
        })->toArray();
        if ($contributors) {
            if (count($contributors) === 1) {
                return $contributors[0] . ' ' . $reference->publication_year;
            }
            elseif (count($contributors) === 2) {
                return $contributors[0] . ' & ' . $contributors[1] . ' ' . $reference->publication_year;
            }
            else {
                return $contributors[0] . ' et al. ' . $reference->publication_year;
            }
        }
        return null;
    }
}
