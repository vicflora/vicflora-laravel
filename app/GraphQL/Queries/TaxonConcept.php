<?php

namespace App\GraphQL\Queries;

use App\Models\TaxonConcept as Concept;
use Illuminate\Support\Facades\Auth;

final class TaxonConcept
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $taxonConcept = Concept::where('guid', $args['id'])->first();

        if ($taxonConcept->publication_status === 'draft'
                && !Auth::check()) {
            return null;
        }

        return $taxonConcept;
    }
}
