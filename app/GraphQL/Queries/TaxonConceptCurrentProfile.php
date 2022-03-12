<?php

namespace App\GraphQL\Queries;

use App\Actions\CreateCurrentProfile;
use App\Models\Profile;
use App\Models\TaxonConcept;
use App\Services\CurrentProfileService;

class TaxonConceptCurrentProfile
{
    /**
     * @param  \App\Models\TaxonConcept  $taxonConcept
     * @param  array<string, mixed>  $args
     */
    public function __invoke(TaxonConcept $taxonConcept, array $args)
    {
        if ($taxonConcept->taxonomicStatus->name === 'accepted') {
            $createCurrentProfile = new CreateCurrentProfile;
            return $createCurrentProfile($taxonConcept);
        }
        return null;
    }
}
