<?php

namespace App\GraphQL\Mutations;

use App\Actions\CreateOrUpdateGroupPerson;
use App\Models\Agent;
use App\Models\AgentType;
use App\Models\GroupPerson;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UpdateAgent
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $input = $args['input'];
        $agent = Agent::where('guid', $input['id'])->first();
        $agent->agent_type_id = 
                AgentType::where('name', $input['agentTypeName'])->value('id');
        $agent->name = $input['name'];
        if (isset($input['last_name'])) {
            $agent->last_name = $input['last_name'];
        }
        if (isset($input['first_name'])) {
            $agent->first_name = $input['first_name'];
        }
        if (isset($input['initials'])) {
            $agent->initials = $input['initials'];
        }
        if (isset($input['email'])) {
            $agent->email = $input['email'];
        }
        if (isset($input['legal_name'])) {
            $agent->legal_name = $input['legal_name'];
        }
        $agent->modified_by_id = Agent::where('user_id', Auth::id())->value('id');
        $agent->version++;
        $agent->save();

        if (isset($input['members']) && $input['members'] 
                && is_array($input['members'])) {
            // delete group people that are not in the input
            $guids = collect($input['members'])->filter(function($item) {
                return $item['id'];
            })->map(function($item) {
                return $item['id'];
            });
            GroupPerson::whereHas('group', function(Builder $query) use ($agent) {
                $query->where('id', $agent->id);
            })->whereNotIn('guid', $guids)->delete();

           $createOrUpdateGroupPerson = new CreateOrUpdateGroupPerson;
            foreach ($input['members'] as $groupPerson) {
                $createOrUpdateGroupPerson($agent->id, $groupPerson);
            }

        }

        return $agent;
    }
}
