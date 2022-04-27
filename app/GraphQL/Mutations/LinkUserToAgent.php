<?php

namespace App\GraphQL\Mutations;

use App\Models\Agent;
use App\Models\AgentType;
use Illuminate\Support\Str;

class LinkUserToAgent
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $input = $args['input'];
        $agent = Agent::whereRaw("lower(email)=lower('{$input['email']}')")
            ->first();
        if (!$agent) {
            $agent = new Agent();
            $agent->guid = Str::uuid();
            $agent->agent_type_id = AgentType::where('name', 'Person')->value('id');
            $agent->created_by_id = 1;
            $agent->name = $input['name'];
            $agent->email = $input['email'];
        }
        $agent->user_id = $input['id'];
        $agent->save();
        return $agent;
    }
}
