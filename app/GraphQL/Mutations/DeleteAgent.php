<?php

namespace App\GraphQL\Mutations;

use App\Models\Agent;

class DeleteAgent
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $agent = Agent::where('guid', $args['id']);
        $agent->delete();
        return $agent;
    }
}
