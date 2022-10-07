<?php

namespace App\GraphQL\Mutations;

use App\Models\Agent;
use App\Models\MultiAccessKey;
use App\Models\MultiAccessKeyCharacter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

final class CreateMultiAccessKeyCharacter
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args): MultiAccessKeyCharacter
    {
        $input = $args['input'];

        $character = new MultiAccessKeyCharacter();
        $character->guid = Str::uuid();
        $character->version = 1;
        $character->created_by_id = Agent::where('user_id', Auth::id())->value('id');
        $character->key_id = 
                MultiAccessKey::where('guid', $input['key']['connect'])
                ->value('id');
        if (isset($input['parent'])) {
            $character->parent_id = MultiAccessKeyCharacter
                    ::where('guid', $input['parent']['connect'])->value('id');
        }
        $character->name = $input['name'];
        $character->type = $input['type'];
        if (isset($input['characterType'])) {
            $character->feature_type = $input['characterType'];
        }
        if (isset($input['description'])) {
            $character->description = $input['description'];
        }
        $character->save();
        return $character;
    }
}
