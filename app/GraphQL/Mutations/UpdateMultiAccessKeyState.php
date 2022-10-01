<?php

namespace App\GraphQL\Mutations;

use App\Models\Agent;
use App\Models\MultiAccessKeyState;
use Illuminate\Support\Facades\Auth;

final class UpdateMultiAccessKeyState
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args): MultiAccessKeyState
    {
        $input = $args['input'];

        $state = MultiAccessKeyState::where('guid', $input['id'])->first();
        $state->modified_by_id = Agent::where('user_id', Auth::id())->value('id');
        if (isset($input['name'])) {
            $state->name = $input['name'];
        }
        if (isset($input['description'])) {
            $state->description = $input['description'];
        }
        $state->increment('version');
        $state->save();
        return $state;
    }
}
