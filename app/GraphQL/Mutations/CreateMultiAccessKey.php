<?php

namespace App\GraphQL\Mutations;

use App\Models\Agent;
use App\Models\MultiAccessKey;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

final class CreateMultiAccessKey
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args): MultiAccessKey
    {
        $input = $args['input'];

        $key = new MultiAccessKey();
        $key->guid = Str::uuid();
        $key->version = 1;
        $key->created_by_id = Agent::where('user_id', Auth::id())->value('id');
        $key->title = $input['title'];
        $key->location = $input['location'];
        if (isset($input['description'])) {
            $key->description = $input['description'];
        }
        $key->save();
        return $key;
    }
}
