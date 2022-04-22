<?php

namespace App\GraphQL\Queries;

use App\Models\Profile;
use App\Models\TaxonConcept;
use Illuminate\Database\Eloquent\Collection;

class TaxonConceptProfiles
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args): Collection
    {
        $taxonConcept = TaxonConcept::where('guid', $args['taxonConceptId'])
            ->first();
        return Profile::where('accepted_id', $taxonConcept->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
