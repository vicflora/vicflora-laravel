<?php

namespace App\GraphQL\Mutations;

use App\Models\Agent;
use App\Models\Profile;
use App\Models\Reference;
use App\Models\TaxonConcept;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CreateProfile
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $input = $args['input'];
        $acceptedConceptId = 
                TaxonConcept::where('guid', $input['acceptedConcept']['connect'])
                ->value('id');
        Profile::where('accepted_id', $acceptedConceptId)
                ->update(['is_current' => false]);
        if (isset($input['id']) && $input['id']) {
            list($guid, $version) = explode(':', $input['id']);
        }
        else {
            $guid = Str::uuid();
            $version = 0;
        }
        $profile = new Profile;
        $profile->guid = $guid;
        $profile->version = ++$version;
        $profile->profile = $input['profile'];
        $profile->taxon_concept_id = 
                TaxonConcept::where('guid', $input['taxonConcept']['connect'])
                ->value('id');
        $profile->accepted_id = $acceptedConceptId;
        $profile->created_by_id = Agent::where('user_id', Auth::id())->value('id');
        if (isset($input['source']) && $input['source']) {
            $profile->source_id = 
                    Reference::where('guid', $input['source']['connect'])
                    ->value('id');
        }
        $profile->is_current = true;
        $profile->is_updated = $version > 1 ? true : false;
        $profile->save();
        return $profile;
    }
}
