<?php

namespace App\GraphQL\Mutations;

use App\Actions\CreateOrUpdateGroupPerson;
use App\Models\Agent;
use App\Models\AgentType;
use App\Models\GroupPerson;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CreateAgent
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $input = $args['input'];
        $agent = new Agent();
        $agent->guid = Str::uuid();
        $agent->agent_type_id = AgentType::where('name', $input['agentTypeName'])->value('id');
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
        $agent->created_by_id = Agent::where('user_id', Auth::id())->value('id');
        $agent->version = 1;
        $agent->save();

        if (isset($input['members']) && $input['members'] 
                && is_array($input['members'])) {
           $createOrUpdateGroupPerson = new CreateOrUpdateGroupPerson;
            foreach ($input['members'] as $groupPerson) {
                $createOrUpdateGroupPerson($agent->id, $groupPerson);
            }

        }

        return $agent;
    }
}
