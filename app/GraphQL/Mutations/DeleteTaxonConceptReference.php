<?php

namespace App\GraphQL\Mutations;

use App\Models\TaxonConcept;
use App\Models\TaxonConceptReference;

class DeleteTaxonConceptReference
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $ref = TaxonConceptReference::where('guid', $args['id'])->first();
        $ref->delete();
        return $ref;
    }
}
