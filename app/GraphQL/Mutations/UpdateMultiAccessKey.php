<?php

namespace App\GraphQL\Mutations;

use App\Models\Agent;
use App\Models\MultiAccessKey;
use Illuminate\Support\Facades\Auth;

final class UpdateMultiAccessKey
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args): MultiAccessKey
    {
        $input = $args['input'];
        $key = MultiAccessKey::where('guid', $input['id'])->first();
        $key->modified_by_id = Agent::where('user_id', Auth::id())->value('id');
        if (isset($input['title'])) {
            $key->title = $input['title'];
        }
        if (isset($input['description'])) {
            $key->description = $input['description'];
        }
        $key->increment('version');
        $key->save();
        return $key;
    }
}
