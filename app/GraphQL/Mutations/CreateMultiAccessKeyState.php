<?php

namespace App\GraphQL\Mutations;

use App\Models\Agent;
use App\Models\MultiAccessKeyFeature;
use App\Models\MultiAccessKeyState;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

final class CreateMultiAccessKeyState
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args): MultiAccessKeyState
    {
        $input = $args['input'];

        $state = new MultiAccessKeyState();
        $state->guid = Str::uuid();
        $state->created_by_id = Agent::where('user_id', Auth::id())->value('id');
        $state->version = 1;
        $state->feature_id = MultiAccessKeyFeature
                ::where('guid', $input['feature']['connect'])->value('id');
        $state->name = $input['name'];
        if (isset($input['description'])) {
            $state->description = $input['description'];
        }
        $state->save();
        return $state;
    }
}
