<?php

namespace App\GraphQL\Queries;

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
            $profile = Profile::where('accepted_id', $taxonConcept->id)
                    ->where('is_current', true)
                    ->first();

            $currentProfileService = new CurrentProfileService($taxonConcept->guid, $profile->profile);
            $profile->profile = $currentProfileService->formatProfile();

            return $profile;
        }
        return null;
    }
}
