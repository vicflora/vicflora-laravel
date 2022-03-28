<?php

namespace App\GraphQL\Mutations;

use App\Models\Agent;
use App\Models\Contributor;
use App\Models\ContributorRole;
use App\Models\Reference;
use Illuminate\Support\Facades\Auth;

class CreateContributors
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $reference = Reference::where('guid', $args['reference'])->first();

        foreach ($args['contributors'] as $contrib) {
            $contributor = new Contributor();
            $contributor->reference_id = $reference->id;
            $contributor->agent_id = 
                    Agent::where('guid', $contrib['agent']['connect'])
                    ->value('id');
            $contributor->sequence = $contrib['sequence'];
            if (isset($contrib['contributorRole'])) {
                $contributor->contributor_role_id = 
                        ContributorRole::where('name', 
                        $contrib['contributorRole'])->value('id');
            }
            $contributor->created_by_id = Auth::id();
            $contributor->save();
        }

        return $reference;
    }
}
