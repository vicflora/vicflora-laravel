<?php

namespace App\GraphQL\Mutations;

use App\Models\TaxonConcept;

class DeleteTaxonConcept
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $taxonConcept = TaxonConcept::where('guid', $args['id'])->first();
        $taxonConcept->delete();
        return $taxonConcept;
    }
}
