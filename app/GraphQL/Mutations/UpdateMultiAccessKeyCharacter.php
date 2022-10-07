<?php

namespace App\GraphQL\Mutations;

use App\Models\Agent;
use App\Models\MultiAccessKeyCharacter;
use Illuminate\Support\Facades\Auth;

final class UpdateMultiAccessKeyCharacter
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args): MultiAccessKeyCharacter
    {
        $input = $args['input'];

        $character = MultiAccessKeyCharacter::where('guid', $input['id'])->first();
        $character->modified_by_id = Agent::where('user_id', Auth::id())->value('id');
        if (isset($input['name'])) {
            $character->name = $input['name'];
        }
        if (isset($input['characterType'])) {
            $character->feature_type = $input['characterType'];
        }
        if (isset($input['description'])) {
            $character->description = $input['description'];
        }
        if (isset($input['parent'])) {
            $character->parent_id = MultiAccessKeyCharacter
                    ::where('guid', $input['parent']['connect'])->value('id');
        }
        $character->increment('version');
        $character->save();
        return $character;
    }
}
